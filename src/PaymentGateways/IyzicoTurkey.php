<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use Illuminate\Support\Facades\Log;
use Iyzipay\FileBase64Encoder;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Iyzilink\IyziLinkSaveProduct;
use Iyzipay\Model\Locale;
use Iyzipay\Options;
use Iyzipay\Request\Iyzilink\IyziLinkSaveProductRequest;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;

use Omnipay\Iyzico\Gateway as IyzicoGateway;

use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;


class IyzicoTurkey extends IyzicoGateway implements PaymentGatewaysInterface
{
    private $gateway;
    private $options;
    private $apiKey;
    private $apiSecret;

    public function __construct()
    {
        $this->gateway = PaymentGateways::where('code', 'iyzico-turkey')->first();

        $this->apiKey = $gateway->parameters['is_test']
            ? $gateway->parameters['test_api_key']
            : $gateway->parameters['live_api_key'];
        $this->apiSecret = $gateway->parameters['is_test']
            ? $gateway->parameters['test_api_secret']
            : $gateway->parameters['live_api_secret'];

        // Initialize Iyzico Options
        $this->options = new Options();
        $this->options->setApiKey($this->apiKey);
        $this->options->setSecretKey($this->apiSecret);

        // Set base URL based on test/production mode
        if (isset($gateway->parameters['is_test']) && $gateway->parameters['is_test']) {
            $this->options->setBaseUrl('https://sandbox-api.iyzipay.com');
        } else {
            $this->options->setBaseUrl('https://api.iyzipay.com');
        }
    }

    public function createCheckoutSession(Invoices $invoice): PaymentCheckoutSessions
    {
        trigger_error('IyzicoTurkey payment gateway is not implemented yet.', E_USER_WARNING);
    }

    public function getCheckoutSession(Accounts $account): PaymentCheckoutSessions
    {
        trigger_error('IyzicoTurkey payment gateway is not implemented yet.', E_USER_WARNING);
    }

    /**
     * Creates an Iyzico Payment Link (IyziLink) for the given invoice.
     *
     * Flow:
     *  1. Guard: invoice already paid -> null
     *  2. Resolve currency & compute amount
     *  3. Build an IyziLink product and get the payment URL
     *  4. Return Payment Link URL
     *
     * Returns null on: already paid, invalid currency, amount <= 0, or Iyzico API error.
     *
     * @param Accounts $account
     * @param Invoices $invoice
     * @param Transactions $transaction
     * @return string|null Payment link URL or null when creation not possible.
     */
    public function createPaymentLink(Accounts $account, Invoices $invoice, Transactions $transaction): ?string
    {
        $isPaid = $invoice->is_paid;
        if ($isPaid) {
            Log::info(__METHOD__ . '::' . __LINE__ . ' - Invoice is already paid', ['invoice_id' => $invoice->id]);
            return null;
        }

        // Get currency and amount from the invoice
        $currency = Currencies::where('id', $invoice->common_currency_id)->first();

        if (!$currency) {
            Log::error(__METHOD__ . '::' . __LINE__ . ' - Currency not found', ['invoice_id' => $invoice->id]);
            return null;
        }

        $currencyCode = strtoupper($currency->code);
        $amount = $invoice->amount;

        if ($amount <= 0) {
            Log::warning(__METHOD__ . '::' . __LINE__ . ' - Invoice amount is not positive', [
                'accounting_invoice_id' => $invoice->id,
                'amount' => $amount,
                'currency' => $currencyCode
            ]);
            return null;
        }

        // Map currency code to Iyzico currency constant
        $iyzicoCurrency = Currency::TL;

        if ($this->gateway->common_currency_id != $invoice->common_currency_id) {
            // Convert invoice currency to common currency
            $amount = ExchangeRateHelper::convert($currency->code, 'TRY', $amount);
        }


        // Apply VAT rate if applicable
        $amount = $amount * (1 + $this->gateway->vat_rate);

        // Format amount to 2 decimal places as required by Iyzico
        $formattedAmount = number_format((float)$amount, 2, '.', '');

        try {
            // Get the logo file path - use public_path() to get absolute path
            $logoPath = public_path(config('leo.iyzico_product_image'));

            // Check if file exists before trying to encode
            if (!file_exists($logoPath)) {
                Log::error(__METHOD__ . '::' . __LINE__ . ' - Logo file not found', [
                    'accounting_invoice_id' => $invoice->id,
                    'path' => $logoPath
                ]);
                return null;
            }

            // The picture must be PNG or JPG only
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $fileExtension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                Log::error(__METHOD__ . '::' . __LINE__ . ' - Logo file must be PNG or JPG', [
                    'accounting_invoice_id' => $invoice->id,
                    'path' => $logoPath,
                    'extension' => $fileExtension
                ]);
                return null;
            }

            $invoiceNumber = "Invoice #" . now()->year . "-" . $invoice->id;

            // Create IyziLink product request
            $request = new IyziLinkSaveProductRequest();
            $request->setLocale(Locale::TR);
            $request->setConversationId($transaction->uuid);
            $request->setName($invoiceNumber);
            $request->setDescription("Payment for " . $invoiceNumber);
            $request->setPrice($formattedAmount);
            $request->setCurrency($iyzicoCurrency);
            $request->setAddressIgnorable(false); // Address is required by Iyzico
            $request->setSoldLimit(1); // Single use payment link
            $request->setInstallmentRequest(false);
            $request->setSourceType('API');
            $request->setStockEnabled(true);
            $request->setStockCount(1);
            $request->setBase64EncodedImage(FileBase64Encoder::encode($logoPath));

            // Create the payment link via Iyzico
            $response = IyziLinkSaveProduct::create($request, $this->options);

            if ($response->getStatus() === 'success' && $response->getUrl()) {
                Log::info(__METHOD__ . '::' . __LINE__ . ' - Payment link created successfully', [
                    'accounting_invoice_id' => $invoice->id,
                    'url' => $response->getUrl()
                ]);
                return $response->getUrl();
            } else {
                Log::error(__METHOD__ . '::' . __LINE__ . ' - Iyzico API error creating payment link', [
                    'accounting_invoice_id' => $invoice->id,
                    'status' => $response->getStatus(),
                    'error_code' => $response->getErrorCode(),
                    'error_message' => $response->getErrorMessage(),
                ]);
                return null;
            }
        } catch (\Throwable $e) {
            Log::error(__METHOD__ . ' Unexpected error creating payment link', [
                'accounting_invoice_id' => $invoice->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }


    /**
     * Handle payment callback from iyzico
     *
     * @param array $callbackData The POST data from iyzico webhook
     * @param array $headers The HTTP headers from the webhook request
     * @return array Result of callback processing
     */
    public function handleCallback(array $callbackData, array $headers = []): array
    {
        try {
            Log::info('iyzico callback received', ['data' => $callbackData, 'headers' => $headers]);

            // Validate webhook signature (X-IYZ-SIGNATURE-V3)
            $signature = $headers['X-IYZ-SIGNATURE-V3'] ?? $headers['x-iyz-signature-v3'] ?? null;

            // TODO: Uncomment signature validation after testing
//            if ($signature && !$this->validateWebhookSignature($callbackData, $signature)) {
//                Log::error('iyzico webhook signature validation failed', [
//                    'signature' => $signature,
//                    'callback_data' => $callbackData
//                ]);
//                return [
//                    'success' => false,
//                    'message' => 'Invalid webhook signature'
//                ];
//            }

            // Extract fields according to HPP Format (used by IyziLink)
            // Fields: iyziEventType, iyziPaymentId, token, paymentConversationId, status
            $iyziEventType = $callbackData['iyziEventType'] ?? null;
            $iyziPaymentId = $callbackData['iyziPaymentId'] ?? null;
            $token = $callbackData['token'] ?? null;
            $paymentConversationId = $callbackData['paymentConversationId'] ?? null;
            $status = $callbackData['status'] ?? null;

            if (!$iyziPaymentId && !$token) {
                return [
                    'success' => false,
                    'message' => 'No iyziPaymentId or token found in callback data'
                ];
            }

            // Extract invoice UUID from paymentConversationId (we set invoice UUID there)
            $transactionUuid = $paymentConversationId;

            if (!$transactionUuid) {
                return [
                    'success' => false,
                    'message' => 'Transaction UUID not found in paymentConversationId'
                ];
            }

            // Check payment status
            // iyzico statuses: 'success', 'failure', 'init_threeds', 'callback_threeds', etc.
            $isSuccessful = strtolower($status) === 'success';

            if ($isSuccessful) {
                Log::info('iyzico payment successful', [
                    'accounting_transaction_id' => $transactionUuid,
                    'payment_id' => $iyziPaymentId,
                    'event_type' => $iyziEventType,
                    'status' => $status
                ]);

                return [
                    'success' => true,
                    'accounting_transaction_id' => $transactionUuid,
                    'transaction_id' => $iyziPaymentId ?? $token,
                    'paid' => true,
                    'payment_method' => 'iyzico',
                    'event_type' => $iyziEventType,
                    'raw_data' => $callbackData
                ];
            } else {
                Log::info('iyzico payment not successful', [
                    'accounting_transaction_id' => $transactionUuid,
                    'status' => $status,
                    'event_type' => $iyziEventType,
                    'error' => $callbackData['errorMessage'] ?? $callbackData['errorCode'] ?? 'Unknown error'
                ]);

                return [
                    'success' => true,  // Callback was processed successfully
                    'paid' => false,    // But payment was not successful
                    'accounting_transaction_id' => $transactionUuid,
                    'message' => $callbackData['errorMessage'] ?? 'Payment failed',
                    'event_type' => $iyziEventType,
                    'raw_data' => $callbackData
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error processing iyzico callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error processing callback: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate Iyzico webhook signature using HMAC-SHA256
     *
     * For HPP Format (IyziLink), a signature is created from:
     * SECRET KEY + iyziEventType + iyziPaymentId + token + paymentConversationId + status
     *
     * @param array $callbackData The webhook payload
     * @param string $signature The X-IYZ-SIGNATURE-V3 header value
     * @return bool True if a signature is valid, false otherwise
     */
    private function validateWebhookSignature(array $callbackData, string $signature): bool
    {
        try {


            // Extract fields according to HPP Format
            $iyziEventType = $callbackData['iyziEventType'] ?? '';
            $iyziPaymentId = $callbackData['iyziPaymentId'] ?? '';
            $token = $callbackData['token'] ?? '';
            $paymentConversationId = $callbackData['paymentConversationId'] ?? '';
            $status = $callbackData['status'] ?? '';

            // Create the key for HMAC (order is important!)
            $key = $this->apiSecret . $iyziEventType . $iyziPaymentId . $token . $paymentConversationId . $status;

            // Generate HMAC SHA256 signature and encode as hex
            $calculatedSignature = hash_hmac('sha256', $key, $this->apiSecret);

            // Compare signatures
            return hash_equals($calculatedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Error validating webhook signature', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}

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
use NextDeveloper\Commons\Database\Models\Currencies;

class IyzicoTurkey implements PaymentGatewaysInterface
{
    private $gateway;
    private $options;

    public function __construct(PaymentGateways $gateway)
    {
        $this->gateway = $gateway;

        // Initialize Iyzico Options
        $this->options = new Options();
        $this->options->setApiKey($gateway->parameters['apiKey']);
        $this->options->setSecretKey($gateway->parameters['apiSecret']);

        // Set base URL based on test/production mode
        if (isset($gateway->parameters['isTest']) && $gateway->parameters['isTest']) {
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
     * @return string|null Payment link URL or null when creation not possible.
     */
    public function createPaymentLink(Accounts $account, Invoices $invoice): ?string
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
                'invoice_id' => $invoice->id,
                'amount' => $amount,
                'currency' => $currencyCode
            ]);
            return null;
        }

        // Map currency code to Iyzico currency constant
        $iyzicoCurrency = $this->mapCurrencyToIyzico($currencyCode);

        // Format amount to 2 decimal places as required by Iyzico
        $formattedAmount = number_format((float) $amount, 2, '.', '');

        try {
            // Get the logo file path - use public_path() to get absolute path
            $logoPath = public_path(config('leo.iyzico_product_image'));

            // Check if file exists before trying to encode
            if (!file_exists($logoPath)) {
                Log::error(__METHOD__ . '::' . __LINE__ . ' - Logo file not found', [
                    'invoice_id' => $invoice->id,
                    'path' => $logoPath
                ]);
                return null;
            }

            // The picture must be PNG or JPG only
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $fileExtension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            if (!in_array($fileExtension, $allowedExtensions)) {
                Log::error(__METHOD__ . '::' . __LINE__ . ' - Logo file must be PNG or JPG', [
                    'invoice_id' => $invoice->id,
                    'path' => $logoPath,
                    'extension' => $fileExtension
                ]);
                return null;
            }

            // Create IyziLink product request
            $request = new IyziLinkSaveProductRequest();
            $request->setLocale(Locale::TR);
            $request->setConversationId($invoice->uuid ?? 'invoice-' . $invoice->id);
            $request->setName("Invoice #{$invoice->invoice_number}");
            $request->setDescription("Payment for Invoice #{$invoice->invoice_number}");
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
                    'invoice_id' => $invoice->id,
                    'url' => $response->getUrl()
                ]);
                return $response->getUrl();
            } else {
                Log::error(__METHOD__ . '::' . __LINE__ . ' - Iyzico API error creating payment link', [
                    'invoice_id' => $invoice->id,
                    'status' => $response->getStatus(),
                    'error_code' => $response->getErrorCode(),
                    'error_message' => $response->getErrorMessage(),
                ]);
                return null;
            }
        } catch (\Throwable $e) {
            Log::error(__METHOD__ . ' Unexpected error creating payment link', [
                'invoice_id' => $invoice->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Map currency code to Iyzico currency constant
     *
     * @param string $currencyCode
     * @return string
     */
    private function mapCurrencyToIyzico(string $currencyCode): string
    {
        // Iyzico supports TRY, USD, EUR, GBP
        switch ($currencyCode) {
            case 'TRY':
            case 'TL':
                return Currency::TL;
            case 'USD':
                return Currency::USD;
            case 'EUR':
                return Currency::EUR;
            case 'GBP':
                return Currency::GBP;
            default:
                Log::warning(__METHOD__ . ' - Unsupported currency, defaulting to TRY', [
                    'currency_code' => $currencyCode
                ]);
                return Currency::TL;
        }
    }

}

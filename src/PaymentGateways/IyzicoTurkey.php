<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use Illuminate\Support\Facades\Log;
use Iyzipay\FileBase64Encoder;
use Iyzipay\Model\Address as IyzipayAddress;
use Iyzipay\Model\BasketItem;
use Iyzipay\Model\BasketItemType;
use Iyzipay\Model\Buyer;
use Iyzipay\Model\Card as IyzipayCard;
use Iyzipay\Model\CardInformation;
use Iyzipay\Model\Currency;
use Iyzipay\Model\Iyzilink\IyziLinkSaveProduct;
use Iyzipay\Model\Locale;
use Iyzipay\Model\PaymentCard;
use Iyzipay\Model\Payment;
use Iyzipay\Model\PaymentChannel;
use Iyzipay\Model\PaymentGroup;
use Iyzipay\Model\ThreedsInitialize;
use Iyzipay\Model\ThreedsPayment;
use Iyzipay\Options;
use Iyzipay\Request\CreateCardRequest;
use Iyzipay\Request\CreatePaymentRequest;
use Iyzipay\Request\CreateThreedsPaymentRequest;
use Iyzipay\Request\Iyzilink\IyziLinkSaveProductRequest;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\CreditCards;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use Omnipay\Iyzico\Gateway as IyzicoGateway;

class IyzicoTurkey extends IyzicoGateway implements PaymentGatewaysInterface
{
    private $gateway;

    /** The account the gateway belongs to, when the caller knows it. */
    private $gatewayOwner;

    private $options;

    private $apiKey;

    private $apiSecret;

    /** The row credentials are read from when the caller does not name one. */
    private const DEFAULT_GATEWAY = 'iyzico-turkey';

    /**
     * @param PaymentGateways|null $gateway The gateway row to work as. Callers that pick
     *                                      a row - `iyzico-link` for payment links, for
     *                                      instance - get that row's credentials; the
     *                                      ones that just want Iyzico pass nothing and
     *                                      get the default row.
     * @param Accounts|null $account The account the gateway belongs to, kept for the
     *                               signature the other gateways share.
     */
    public function __construct($gateway = null, $account = null)
    {
        //  Gateway credentials are system configuration, not user-owned, so the
        //  authorization scope must be bypassed (this runs in request/admin contexts
        //  where the gateway's owning account is not the current account).
        $this->gateway = $gateway instanceof PaymentGateways
            ? $gateway
            : self::getGatewayByName(self::DEFAULT_GATEWAY);

        $this->gatewayOwner = $account;

        if (! $this->gateway) {
            throw new \RuntimeException('There is no Iyzico payment gateway configured.');
        }

        [$this->apiKey, $this->apiSecret, $isTest] = $this->resolveCredentials($this->gateway);

        // Initialize Iyzico Options
        $this->options = new Options;
        $this->options->setApiKey($this->apiKey);
        $this->options->setSecretKey($this->apiSecret);

        // Set base URL based on test/production mode
        $this->options->setBaseUrl($isTest
            ? 'https://sandbox-api.iyzipay.com'
            : 'https://api.iyzipay.com');
    }

    /**
     * The key, the secret and whether they are the sandbox ones.
     *
     * A row that carries no credentials of its own borrows them from the default
     * gateway: several rows exist for the one Iyzico merchant (one per product, such as
     * the payment link one), and only the main row is guaranteed to be filled in.
     *
     * `is_test` is read leniently because it reaches the database both as a boolean and
     * as a string, and the string "false" is true to PHP.
     *
     * @return array{0: string|null, 1: string|null, 2: bool}
     */
    private function resolveCredentials(PaymentGateways $gateway): array
    {
        $parameters = (array) $gateway->parameters;
        $isTest = filter_var($parameters['is_test'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $keyField = $isTest ? 'test_api_key' : 'live_api_key';
        $secretField = $isTest ? 'test_api_secret' : 'live_api_secret';

        if (! empty($parameters[$keyField]) && ! empty($parameters[$secretField])) {
            return [$parameters[$keyField], $parameters[$secretField], $isTest];
        }

        if ($gateway->name !== self::DEFAULT_GATEWAY) {
            $fallback = self::getGatewayByName(self::DEFAULT_GATEWAY);

            if ($fallback) {
                Log::warning(__METHOD__.' - The gateway '.$gateway->name.' has no '
                    .($isTest ? 'test' : 'live').' credentials, falling back to '
                    .self::DEFAULT_GATEWAY.'.', ['accounting_payment_gateway_id' => $gateway->id]);

                return $this->resolveCredentials($fallback);
            }
        }

        Log::error(__METHOD__.' - The gateway '.$gateway->name.' has no '
            .($isTest ? 'test' : 'live').' credentials.', [
                'accounting_payment_gateway_id' => $gateway->id,
            ]);

        return [null, null, $isTest];
    }

    private static function getGatewayByName(string $name): ?PaymentGateways
    {
        return PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('name', $name)
            ->first();
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
     * @return string|null Payment link URL or null when creation not possible.
     */
    public function createPaymentLink(Accounts $account, Invoices $invoice, Transactions $transaction): ?string
    {
        $isPaid = $invoice->is_paid;
        if ($isPaid) {
            Log::info(__METHOD__.'::'.__LINE__.' - Invoice is already paid', ['invoice_id' => $invoice->id]);

            return null;
        }

        // Get currency and amount from the invoice
        $currency = Currencies::where('id', $invoice->common_currency_id)->first();

        if (! $currency) {
            Log::error(__METHOD__.'::'.__LINE__.' - Currency not found', ['invoice_id' => $invoice->id]);

            return null;
        }

        $currencyCode = strtoupper($currency->code);
        $amount = $invoice->amount;

        if ($amount <= 0) {
            Log::warning(__METHOD__.'::'.__LINE__.' - Invoice amount is not positive', [
                'accounting_invoice_id' => $invoice->id,
                'amount' => $amount,
                'currency' => $currencyCode,
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
        $formattedAmount = number_format((float) $amount, 2, '.', '');

        try {
            // Get the logo file path - use public_path() to get absolute path
            $logoPath = public_path(config('leo.iyzico_product_image'));

            // Check if file exists before trying to encode
            if (! file_exists($logoPath)) {
                Log::error(__METHOD__.'::'.__LINE__.' - Logo file not found', [
                    'accounting_invoice_id' => $invoice->id,
                    'path' => $logoPath,
                ]);

                return null;
            }

            // The picture must be PNG or JPG only
            $allowedExtensions = ['png', 'jpg', 'jpeg'];
            $fileExtension = strtolower(pathinfo($logoPath, PATHINFO_EXTENSION));
            if (! in_array($fileExtension, $allowedExtensions)) {
                Log::error(__METHOD__.'::'.__LINE__.' - Logo file must be PNG or JPG', [
                    'accounting_invoice_id' => $invoice->id,
                    'path' => $logoPath,
                    'extension' => $fileExtension,
                ]);

                return null;
            }

            $invoiceNumber = 'Invoice #'.now()->year.'-'.$invoice->id;

            // Create IyziLink product request
            $request = new IyziLinkSaveProductRequest;
            $request->setLocale(Locale::TR);
            $request->setConversationId($transaction->uuid);
            $request->setName($invoiceNumber);
            $request->setDescription('Payment for '.$invoiceNumber);
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
                Log::info(__METHOD__.'::'.__LINE__.' - Payment link created successfully', [
                    'accounting_invoice_id' => $invoice->id,
                    'url' => $response->getUrl(),
                ]);

                return $response->getUrl();
            } else {
                Log::error(__METHOD__.'::'.__LINE__.' - Iyzico API error creating payment link', [
                    'accounting_invoice_id' => $invoice->id,
                    'status' => $response->getStatus(),
                    'error_code' => $response->getErrorCode(),
                    'error_message' => $response->getErrorMessage(),
                ]);

                return null;
            }
        } catch (\Throwable $e) {
            Log::error(__METHOD__.' Unexpected error creating payment link', [
                'accounting_invoice_id' => $invoice->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Creates an Iyzico Payment Link (IyziLink) from a raw amount + currency, used by the
     * credit top-up flow (no invoice). The $conversationId round-trips back as
     * paymentConversationId on the webhook and carries the signed top-up intent.
     */
    public function createTopupLink(float $amount, string $currencyCode, string $conversationId, string $name, bool $applyVat = true): ?string
    {
        if ($amount <= 0) {
            return null;
        }

        //  IyziLink charges in TRY; convert when the requested currency differs.
        if (strtoupper($currencyCode) !== 'TRY') {
            $amount = ExchangeRateHelper::convert($currencyCode, 'TRY', $amount);
        }

        //  Credit top-ups add VAT on top; product purchases charge the catalog price as-is
        //  (KDV-included, matching the displayed price and the Stripe checkout).
        if ($applyVat) {
            $amount = $amount * (1 + ($this->gateway->vat_rate ?? 0));
        }

        $formattedAmount = number_format((float) $amount, 2, '.', '');

        try {
            $logoPath = public_path(config('leo.iyzico_product_image'));

            if (! file_exists($logoPath)) {
                Log::error(__METHOD__.'::'.__LINE__.' - Logo file not found', ['path' => $logoPath]);

                return null;
            }

            $request = new IyziLinkSaveProductRequest;
            $request->setLocale(Locale::TR);
            $request->setConversationId($conversationId);
            $request->setName($name);
            $request->setDescription($name);
            $request->setPrice($formattedAmount);
            $request->setCurrency(Currency::TL);
            $request->setAddressIgnorable(false);
            $request->setSoldLimit(1);
            $request->setInstallmentRequest(false);
            $request->setSourceType('API');
            $request->setStockEnabled(true);
            $request->setStockCount(1);
            $request->setBase64EncodedImage(FileBase64Encoder::encode($logoPath));

            $response = IyziLinkSaveProduct::create($request, $this->options);

            if ($response->getStatus() === 'success' && $response->getUrl()) {
                return $response->getUrl();
            }

            Log::error(__METHOD__.'::'.__LINE__.' - Iyzico API error creating top-up link', [
                'status' => $response->getStatus(),
                'error_message' => $response->getErrorMessage(),
            ]);

            return null;
        } catch (\Throwable $e) {
            Log::error(__METHOD__.' Unexpected error creating top-up link', [
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Initializes a 3-D Secure card payment. Returns the base64 3DS HTML form to render
     * (it auto-submits to the bank); the bank then POSTs the result to $callbackUrl.
     *
     * @param  array{address:string,city:string,country:string,zipCode:?string,ip:?string}  $billing
     * @return array{success:bool, html:?string, paymentId:?string, error:?string}
     */
    public function initiate3dsPayment(
        CreditCards $card,
        float $amount,
        string $conversationId,
        Users $buyer,
        array $billing,
        string $callbackUrl
    ): array {
        try {
            $price = number_format($amount, 2, '.', '');

            $paymentCard = new PaymentCard;
            $paymentCard->setCardHolderName($card->cc_holder_name);
            $paymentCard->setCardNumber(str_replace(' ', '', decrypt($card->cc_number)));
            $paymentCard->setExpireMonth($card->cc_month);
            $paymentCard->setExpireYear($card->cc_year);
            $paymentCard->setCvc($card->cc_cvv);
            $paymentCard->setRegisterCard(0);

            $iyzicoBuyer = new Buyer;
            $iyzicoBuyer->setId((string) $buyer->id);
            $iyzicoBuyer->setName($buyer->name ?: 'Customer');
            $iyzicoBuyer->setSurname($buyer->surname ?: 'Customer');
            $iyzicoBuyer->setEmail($buyer->email);
            $iyzicoBuyer->setIdentityNumber($buyer->nin ?: '11111111111');
            $iyzicoBuyer->setRegistrationAddress($billing['address']);
            $iyzicoBuyer->setCity($billing['city']);
            $iyzicoBuyer->setCountry($billing['country']);
            $iyzicoBuyer->setZipCode($billing['zipCode'] ?? null);
            $iyzicoBuyer->setIp($billing['ip'] ?? '0.0.0.0');

            $address = new IyzipayAddress;
            $address->setContactName($card->cc_holder_name);
            $address->setCity($billing['city']);
            $address->setCountry($billing['country']);
            $address->setAddress($billing['address']);
            $address->setZipCode($billing['zipCode'] ?? null);

            $basketItem = new BasketItem;
            $basketItem->setId('credit-topup');
            $basketItem->setName('Account credit top-up');
            $basketItem->setCategory1('Cloud Service');
            $basketItem->setItemType(BasketItemType::VIRTUAL);
            $basketItem->setPrice($price);

            $request = new CreatePaymentRequest;
            $request->setLocale(Locale::TR);
            $request->setConversationId($conversationId);
            $request->setPrice($price);
            $request->setPaidPrice($price);
            $request->setCurrency(Currency::TL);
            $request->setInstallment(1);
            $request->setBasketId('topup-'.$card->id);
            $request->setPaymentChannel(PaymentChannel::WEB);
            $request->setPaymentGroup(PaymentGroup::PRODUCT);
            $request->setCallbackUrl($callbackUrl);
            $request->setPaymentCard($paymentCard);
            $request->setBuyer($iyzicoBuyer);
            $request->setShippingAddress($address);
            $request->setBillingAddress($address);
            $request->setBasketItems([$basketItem]);

            $init = ThreedsInitialize::create($request, $this->options);

            if ($init->getStatus() === 'success' && $init->getHtmlContent()) {
                //  getHtmlContent() is already base64-decoded by the SDK; re-encode for JSON transport.
                return [
                    'success' => true,
                    'html' => base64_encode($init->getHtmlContent()),
                    'paymentId' => $init->getPaymentId(),
                    'error' => null,
                ];
            }

            Log::error(__METHOD__.' - Iyzico 3DS initialize failed', [
                'status' => $init->getStatus(),
                'error_code' => $init->getErrorCode(),
                'error_message' => $init->getErrorMessage(),
            ]);

            return ['success' => false, 'html' => null, 'paymentId' => null, 'error' => $init->getErrorMessage() ?: 'Could not initialize 3D Secure.'];
        } catch (\Throwable $e) {
            Log::error(__METHOD__.' - Unexpected error initializing 3DS', ['exception' => get_class($e), 'message' => $e->getMessage()]);

            return ['success' => false, 'html' => null, 'paymentId' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Completes a 3-D Secure payment after the bank callback.
     *
     * @return array{success:bool, paymentId:?string, error:?string}
     */
    public function complete3dsPayment(string $paymentId, string $conversationData, string $conversationId): array
    {
        try {
            $request = new CreateThreedsPaymentRequest;
            $request->setLocale(Locale::TR);
            $request->setConversationId($conversationId);
            $request->setPaymentId($paymentId);
            $request->setConversationData($conversationData);

            $payment = ThreedsPayment::create($request, $this->options);

            if ($payment->getStatus() === 'success') {
                return ['success' => true, 'paymentId' => $payment->getPaymentId(), 'error' => null];
            }

            Log::error(__METHOD__.' - Iyzico 3DS completion failed', [
                'status' => $payment->getStatus(),
                'error_message' => $payment->getErrorMessage(),
            ]);

            return ['success' => false, 'paymentId' => $payment->getPaymentId(), 'error' => $payment->getErrorMessage() ?: 'Payment could not be completed.'];
        } catch (\Throwable $e) {
            Log::error(__METHOD__.' - Unexpected error completing 3DS', ['message' => $e->getMessage()]);

            return ['success' => false, 'paymentId' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Validates the HMAC-SHA256 signature on a 3DS callback (HPP order:
     * conversationData:conversationId:mdStatus:paymentId:status).
     */
    public function validate3dsSignature(array $cb): bool
    {
        $data = ($cb['conversationData'] ?? '').':'
            .($cb['conversationId'] ?? '').':'
            .($cb['mdStatus'] ?? '').':'
            .($cb['paymentId'] ?? '').':'
            .($cb['status'] ?? '');

        $calculated = bin2hex(hash_hmac('sha256', $data, $this->apiSecret, true));

        return hash_equals($calculated, $cb['signature'] ?? '');
    }

    /**
     * Handle payment callback from iyzico
     *
     * @param  array  $callbackData  The POST data from iyzico webhook
     * @param  array  $headers  The HTTP headers from the webhook request
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

            if (! $iyziPaymentId && ! $token) {
                return [
                    'success' => false,
                    'message' => 'No iyziPaymentId or token found in callback data',
                ];
            }

            // Extract invoice UUID from paymentConversationId (we set invoice UUID there)
            $transactionUuid = $paymentConversationId;

            if (! $transactionUuid) {
                return [
                    'success' => false,
                    'message' => 'Transaction UUID not found in paymentConversationId',
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
                    'status' => $status,
                ]);

                return [
                    'success' => true,
                    'accounting_transaction_id' => $transactionUuid,
                    'transaction_id' => $iyziPaymentId ?? $token,
                    'paid' => true,
                    'payment_method' => 'iyzico',
                    'event_type' => $iyziEventType,
                    'raw_data' => $callbackData,
                ];
            } else {
                Log::info('iyzico payment not successful', [
                    'accounting_transaction_id' => $transactionUuid,
                    'status' => $status,
                    'event_type' => $iyziEventType,
                    'error' => $callbackData['errorMessage'] ?? $callbackData['errorCode'] ?? 'Unknown error',
                ]);

                return [
                    'success' => true,  // Callback was processed successfully
                    'paid' => false,    // But payment was not successful
                    'accounting_transaction_id' => $transactionUuid,
                    'message' => $callbackData['errorMessage'] ?? 'Payment failed',
                    'event_type' => $iyziEventType,
                    'raw_data' => $callbackData,
                ];
            }

        } catch (\Exception $e) {
            Log::error('Error processing iyzico callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => 'Error processing callback: '.$e->getMessage(),
            ];
        }
    }

    /**
     * Validate Iyzico webhook signature using HMAC-SHA256
     *
     * For HPP Format (IyziLink), a signature is created from:
     * SECRET KEY + iyziEventType + iyziPaymentId + token + paymentConversationId + status
     *
     * @param  array  $callbackData  The webhook payload
     * @param  string  $signature  The X-IYZ-SIGNATURE-V3 header value
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
            $key = $this->apiSecret.$iyziEventType.$iyziPaymentId.$token.$paymentConversationId.$status;

            // Generate HMAC SHA256 signature and encode as hex
            $calculatedSignature = hash_hmac('sha256', $key, $this->apiSecret);

            // Compare signatures
            return hash_equals($calculatedSignature, $signature);
        } catch (\Exception $e) {
            Log::error('Error validating webhook signature', [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }


    /**
     * Charges a card straight away - no 3-D Secure step, so the caller gets the bank's
     * answer in the same request.
     *
     * The card is charged through its vault entry (cardUserKey + cardToken) when it has
     * one, which keeps the PAN out of the request; a card that was stored before the
     * vault existed is charged with the number we hold.
     *
     * @param array{address:string, city:string, country:string, zipCode:?string, ip:?string} $billing
     * @param array{id:string, name:string, category:?string} $basket What the charge is for.
     * @param string $locale The language Iyzico writes its refusal in - it is shown to
     *                       whoever asked for the charge, so it follows their language
     *                       rather than the gateway's country.
     *
     * @return array{success:bool, paymentId:?string, errorCode:?string, error:?string}
     */
    public function chargeCard(
        CreditCards $card,
        float $amount,
        string $currencyCode,
        string $conversationId,
        Users $buyer,
        array $billing,
        array $basket,
        string $locale = Locale::TR
    ): array {
        try {
            $price = number_format($amount, 2, '.', '');

            $paymentCard = new PaymentCard;

            if ($card->is_stored_at_pg && $card->pg_card_token && $card->pg_card_user_key) {
                $paymentCard->setCardUserKey($card->pg_card_user_key);
                $paymentCard->setCardToken($card->pg_card_token);
            } else {
                $paymentCard->setCardHolderName($card->cc_holder_name);
                $paymentCard->setCardNumber(str_replace(' ', '', decrypt($card->cc_number)));
                $paymentCard->setExpireMonth($card->cc_month);
                $paymentCard->setExpireYear($card->cc_year);
                $paymentCard->setCvc($card->cc_cvv);
                $paymentCard->setRegisterCard(0);
            }

            $iyzicoBuyer = new Buyer;
            $iyzicoBuyer->setId((string) $buyer->id);
            $iyzicoBuyer->setName($buyer->name ?: 'Customer');
            $iyzicoBuyer->setSurname($buyer->surname ?: 'Customer');
            $iyzicoBuyer->setEmail($buyer->email);
            $iyzicoBuyer->setIdentityNumber($buyer->nin ?: '11111111111');
            $iyzicoBuyer->setRegistrationAddress($billing['address']);
            $iyzicoBuyer->setCity($billing['city']);
            $iyzicoBuyer->setCountry($billing['country']);
            $iyzicoBuyer->setZipCode($billing['zipCode'] ?? null);
            $iyzicoBuyer->setIp($billing['ip'] ?? '0.0.0.0');

            $address = new IyzipayAddress;
            $address->setContactName($card->cc_holder_name ?: ($buyer->name ?: 'Customer'));
            $address->setCity($billing['city']);
            $address->setCountry($billing['country']);
            $address->setAddress($billing['address']);
            $address->setZipCode($billing['zipCode'] ?? null);

            $basketItem = new BasketItem;
            $basketItem->setId($basket['id']);
            $basketItem->setName($basket['name']);
            $basketItem->setCategory1($basket['category'] ?? 'Cloud Service');
            $basketItem->setItemType(BasketItemType::VIRTUAL);
            $basketItem->setPrice($price);

            $request = new CreatePaymentRequest;
            $request->setLocale($locale === Locale::EN ? Locale::EN : Locale::TR);
            $request->setConversationId($conversationId);
            $request->setPrice($price);
            $request->setPaidPrice($price);
            $request->setCurrency(self::iyzicoCurrency($currencyCode));
            $request->setInstallment(1);
            $request->setBasketId($basket['id']);
            $request->setPaymentChannel(PaymentChannel::WEB);
            $request->setPaymentGroup(PaymentGroup::PRODUCT);
            $request->setPaymentCard($paymentCard);
            $request->setBuyer($iyzicoBuyer);
            $request->setShippingAddress($address);
            $request->setBillingAddress($address);
            $request->setBasketItems([$basketItem]);

            $payment = Payment::create($request, $this->options);

            if ($payment->getStatus() === 'success') {
                return [
                    'success' => true,
                    'paymentId' => $payment->getPaymentId(),
                    'errorCode' => null,
                    'error' => null,
                ];
            }

            Log::error(__METHOD__.' - Iyzico charge failed', [
                'credit_card_id' => $card->id,
                'conversation_id' => $conversationId,
                'error_code' => $payment->getErrorCode(),
                'error_message' => $payment->getErrorMessage(),
            ]);

            return [
                'success' => false,
                'paymentId' => $payment->getPaymentId(),
                'errorCode' => $payment->getErrorCode(),
                'error' => $payment->getErrorMessage() ?: 'The payment was declined.',
            ];
        } catch (\Throwable $e) {
            Log::error(__METHOD__.' - Unexpected error charging card', [
                'credit_card_id' => $card->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return ['success' => false, 'paymentId' => null, 'errorCode' => null, 'error' => $e->getMessage()];
        }
    }

    /**
     * Iyzico names its currencies with its own constants; anything it does not know is
     * charged in Lira, which is the only currency the Turkish gateway settles in anyway.
     */
    private static function iyzicoCurrency(string $currencyCode): string
    {
        return match (strtoupper($currencyCode)) {
            'USD' => Currency::USD,
            'EUR' => Currency::EUR,
            'GBP' => Currency::GBP,
            default => Currency::TL,
        };
    }

    /**
     * Store a credit card at Iyzico Card Storage.
     *
     * Uses cardUserKey if another card for the same account is already stored,
     * otherwise creates a new Iyzico user with the account's email.
     *
     * @return array{success: bool, cardUserKey: string|null, cardToken: string|null, errorMessage: string|null}
     */
    public function storeCard(CreditCards $card): array
    {
        try {
            $request = new CreateCardRequest;
            $request->setLocale(Locale::TR);
            $request->setConversationId($card->uuid);

            $existingCardWithKey = CreditCards::query()
                ->where('iam_account_id', $card->iam_account_id)
                ->where('pg_provider', 'iyzico-turkey')
                ->whereNotNull('pg_card_user_key')
                ->where('id', '!=', $card->id)
                ->first();

            if ($existingCardWithKey) {
                $request->setCardUserKey($existingCardWithKey->pg_card_user_key);
            } else {
                $user = Users::find($card->iam_user_id);

                if (! $user) {
                    Log::error(__METHOD__.' - User not found for card', ['credit_card_id' => $card->id]);

                    return ['success' => false, 'cardUserKey' => null, 'cardToken' => null, 'errorMessage' => 'User not found'];
                }

                $request->setEmail($user->email);
                $request->setExternalId((string) $card->iam_account_id);
            }

            $cardInformation = new CardInformation;
            $cardInformation->setCardAlias($card->name);
            $cardInformation->setCardHolderName($card->cc_holder_name);
            $cardInformation->setCardNumber(decrypt($card->cc_number));
            $cardInformation->setExpireMonth($card->cc_month);
            $cardInformation->setExpireYear($card->cc_year);
            $request->setCard($cardInformation);

            $response = IyzipayCard::create($request, $this->options);

            if ($response->getStatus() === 'success') {
                Log::info(__METHOD__.' - Card stored at Iyzico', [
                    'credit_card_id' => $card->id,
                    'card_user_key' => $response->getCardUserKey(),
                    'card_token' => $response->getCardToken(),
                ]);

                return [
                    'success' => true,
                    'cardUserKey' => $response->getCardUserKey(),
                    'cardToken' => $response->getCardToken(),
                    'errorMessage' => null,
                ];
            }

            Log::error(__METHOD__.' - Iyzico card storage failed', [
                'credit_card_id' => $card->id,
                'error_code' => $response->getErrorCode(),
                'error_message' => $response->getErrorMessage(),
            ]);

            return [
                'success' => false,
                'cardUserKey' => null,
                'cardToken' => null,
                'errorMessage' => $response->getErrorMessage(),
            ];
        } catch (\Throwable $e) {
            Log::error(__METHOD__.' - Unexpected error storing card at Iyzico', [
                'credit_card_id' => $card->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'cardUserKey' => null,
                'cardToken' => null,
                'errorMessage' => $e->getMessage(),
            ];
        }
    }
}

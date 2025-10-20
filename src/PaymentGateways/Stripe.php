<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Accounting\Database\Models\Transactions;

class Stripe implements PaymentGatewaysInterface
{
    private $gateway;

    // Stripe zero-decimal currencies
    // Source: https://stripe.com/docs/currencies#zero-decimal

    const array ZERO_DECIMAL_CURRENCIES = [
        'BIF',
        'CLP',
        'DJF',
        'GNF',
        'JPY',
        'KMF',
        'KRW',
        'MGA',
        'PYG',
        'RWF',
        'UGX',
        'VND',
        'VUV',
        'XAF',
        'XOF',
        'XPF'
    ];

    public function __construct(PaymentGateways $gateway)
    {
        if ($gateway->parameters['is_test']) {
            $this->gateway = new \Stripe\StripeClient($gateway->parameters['test_api_secret']);
        } else {
            $this->gateway = new \Stripe\StripeClient($gateway->parameters['live_api_secret']);
        }
    }

    public function getCheckoutSession(Accounts $account): \NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions
    {
        $checkoutSession = PaymentCheckoutSessions::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $account->id)
            ->first();

        $iamAccount = AccountingHelper::getIamAccount($account);
        $iamUser = UserHelper::getAccountOwner($iamAccount);

        if (!$checkoutSession) {
            $stripeCustomer = $this->getStripeCustomer($account);

            //  Here this means that we dont have a checkout session. We need to create a checkout session with predefined Membership product
            $session = $this->gateway->checkout->sessions->create([
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
                'customer' => $stripeCustomer->id,
                'line_items' => [
                    [
                        'price' => config('leo.payment.stripe.default_membership_product_id'), // Assuming product_id is the Stripe price ID
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
            ]);

            $distributorAccount = AccountingHelper::getDistributorAccount($account);
            $gateway = AccountingHelper::getPaymentGatewayOfDistributor($distributorAccount);

            $checkoutSession = PaymentCheckoutSessions::create([
                'accounting_account_id' => $account->id,
                'accounting_payment_gateway_id' => $gateway->id,
                'session_data' => [
                    'id' => $session->id,
                    'customer' => $stripeCustomer->id,
                    'status' => $session->status,
                    'expires_at' => $session->expires_at,
                    'approval_url' => $session->url
                ]
            ]);
        }

        return $checkoutSession;
    }

    public function getStripeCustomer(Accounts $account)
    {
        $iamAccount = AccountingHelper::getIamAccount($account);
        $iamUser = UserHelper::getAccountOwner($iamAccount);

        $customers = $this->gateway->customers->search([
            'query' => "email:'{$iamUser->email}'",
        ]);

        if (!$customers->data || count($customers->data) == 0) {
            //  If we don't have a customer, we need to create one
            $customer = $this->gateway->customers->create([
                'email' => $iamUser->email,
                'name' => $iamUser->name,
                'metadata' => [
                    'account_id' => $account->id,
                    'iam_account_id' => $iamAccount->id,
                ],
            ]);

            return $customer;
        } else {
            //  If we have a customer, we will return the first one
            return $customers->data[0];
        }
    }

    /**
     * Creates a Stripe Payment Link for the given invoice.
     *
     * Flow:
     *  1. Guard: invoice already paid -> null
     *  2. Resolve currency & compute minor units (handling zero-decimal currencies)
     *  3. Build a one-off Price (ephemeral) then Payment Link
     *  4. Return Payment Link URL
     *
     * Returns null on: already paid, invalid currency, computed amount <= 0, below min (if configured), or Stripe error.
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

        // get currency and amount from the invoice
        $currency = Currencies::where('id', $invoice->common_currency_id)
            ->first();

        if (!$currency) {
            Log::error(__METHOD__ . '::' . __LINE__ . ' - Currency not found', ['currency_code' => $invoice->common_currency_id]);
            return null;
        }

        $currencyCode = strtoupper($currency->code);
        $unitAmount = $this->convertToMinorUnits($invoice->amount, $currencyCode);

        if ($unitAmount <= 0) {
            Log::warning(__METHOD__ . '::' . __LINE__ . ' - Calculated unit amount is not positive', [
                'accounting_invoice_id' => $invoice->id,
                'raw_amount' => $invoice->amount,
                'unit_amount' => $unitAmount,
                'currency' => $currencyCode
            ]);
        }

        try {
            // create payment link for the invoice with safe integer amount
            $price = $this->gateway->prices->create([
                'unit_amount' => $unitAmount,
                'currency' => strtolower($currencyCode),
                'product_data' => [
                    'name' => "Invoice #" . now()->year . "-" .  $invoice->id,
                    'metadata' => [
                        'accounting_transaction_id' => $transaction->uuid,
                    ],
                ],
            ]);

            $paymentLink = $this->gateway->paymentLinks->create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'accounting_transaction_id' => $transaction->uuid,
                ],
            ]);

            return $paymentLink->url;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error(__METHOD__ . ' Stripe API error creating payment link', [
                'accounting_invoice_id' => $invoice->id,
                'stripe_error_type' => method_exists($e, 'getError') && $e->getError() ? $e->getError()->type : null,
                'stripe_error_code' => method_exists($e, 'getError') && $e->getError() ? $e->getError()->code : null,
                'message' => $e->getMessage(),
            ]);
            return null;
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
     * Convert a decimal amount to Stripe's expected minor unit integer safely.
     * Handles zero-decimal currencies and floating point precision issues.
     */
    private function convertToMinorUnits($amount, string $currencyCode): int
    {
        $zeroDecimalCurrencies = self::ZERO_DECIMAL_CURRENCIES;

        if (in_array($currencyCode, $zeroDecimalCurrencies)) {
            return (int) round($amount);
        }

        if (function_exists('bcmul')) {
            return (int) bcmul((string) $amount, '100', 0);
        }

        return (int) round(((float) $amount) * 100);
    }

    /**
     * Handle payment callback from Stripe
     *
     * @param array $callbackData The webhook data from Stripe
     * @param array $headers The HTTP headers from the webhook request (for signature validation)
     * @return array Result of callback processing
     */
    public function handleCallback(array $callbackData, array $headers = []): array
    {
        try {
            Log::info('Stripe callback received', ['data' => $callbackData]);

            // Stripe sends event type to identify the webhook
            $eventType = $callbackData['type'] ?? null;

            if (!$eventType) {
                return [
                    'success' => false,
                    'message' => 'No event type found in callback data'
                ];
            }

            // Handle different Stripe event types
            switch ($eventType) {
                case 'payment_intent.succeeded':
                case 'checkout.session.completed':
                    return $this->handleSuccessfulPayment($callbackData);

                case 'payment_intent.payment_failed':
                case 'checkout.session.expired':
                    return $this->handleFailedPayment($callbackData);

                default:
                    Log::info('Unhandled Stripe event type', ['type' => $eventType]);
                    return [
                        'success' => true,
                        'message' => 'Event type not processed: ' . $eventType
                    ];
            }

        } catch (\Exception $e) {
            Log::error('Error processing Stripe callback', [
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
     * Handle successful payment from Stripe
     *
     * @param array $eventData
     * @return array
     */
    private function handleSuccessfulPayment(array $eventData): array
    {
        $data = $eventData['data']['object'] ?? [];

        // Extract transaction ID from metadata
        $transactionId = $data['metadata']['accounting_transaction_id'] ?? null;
        $paymentIntentId = $data['id'] ?? $data['payment_intent'] ?? null;

        if (!$transactionId) {
            Log::warning('Transaction ID not found in Stripe callback metadata', ['data' => $data]);
            return [
                'success' => false,
                'message' => 'Transaction ID not found in metadata'
            ];
        }

        return [
            'success' => true,
            'accounting_transaction_id' => $transactionId,
            'transaction_id' => $paymentIntentId,
            'paid' => true,
            'payment_method' => 'stripe',
            'raw_data' => $eventData
        ];
    }

    /**
     * Handle failed payment from Stripe
     *
     * @param array $eventData
     * @return array
     */
    private function handleFailedPayment(array $eventData): array
    {
        $data = $eventData['data']['object'] ?? [];
        $transactionId = $data['metadata']['accounting_transaction_id'] ?? null;

        Log::info('Stripe payment failed', [
            'accounting_transaction_id' => $transactionId,
            'event_type' => $eventData['type'] ?? 'unknown'
        ]);

        return [
            'success' => true,
            'paid' => false,
            'accounting_transaction_id' => $transactionId,
            'message' => 'Payment failed or expired',
            'raw_data' => $eventData
        ];
    }
}

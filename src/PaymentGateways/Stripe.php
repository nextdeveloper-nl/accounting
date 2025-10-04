<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class Stripe implements PaymentGatewaysInterface
{
    private $gateway;

    // Stripe zero-decimal currencies
    // Source: https://stripe.com/docs/currencies#zero-decimal

    const ZERO_DECIMAL_CURRENCIES = [
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
     * @param \NextDeveloper\Accounting\Database\Models\Invoices $invoice
     * @return string|null Payment link URL or null when creation not possible.
     */
    public function createPaymentLink(Accounts $account, \NextDeveloper\Accounting\Database\Models\Invoices $invoice): ?string
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
            Log::error(__METHOD__ . '::' . __LINE__ . ' - Currency not found', ['currency_code' => $invoice->currency]);
            return null;
        }

        $currencyCode = strtoupper($currency->code);
        $unitAmount = $this->convertToMinorUnits($invoice->amount, $currencyCode);

        if ($unitAmount <= 0) {
            Log::warning(__METHOD__ . '::' . __LINE__ . ' - Calculated unit amount is not positive', [
                'invoice_id' => $invoice->id,
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
                    'name' => "Invoice #{$invoice->id}",
                    'metadata' => [
                        'invoice_id' => $invoice->id,
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
                    'invoice_id' => $invoice->id,
                    'accounting_account_id' => $account->id,
                ],
            ]);

            return $paymentLink->url;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error(__METHOD__ . ' Stripe API error creating payment link', [
                'invoice_id' => $invoice->id,
                'stripe_error_type' => method_exists($e, 'getError') && $e->getError() ? $e->getError()->type : null,
                'stripe_error_code' => method_exists($e, 'getError') && $e->getError() ? $e->getError()->code : null,
                'message' => $e->getMessage(),
            ]);
            return null;
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
}

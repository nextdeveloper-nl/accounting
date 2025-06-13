<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use NextDeveloper\Accounting\Helpers\AccountingHelper;
use Helpers\InvoiceHelper;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Exceptions\CheckoutSessionException;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class Stripe implements PaymentGatewaysInterface
{
    private $gateway;

    public function __construct(PaymentGateways $gateway)
    {
        if($gateway->parameters['is_test']) {
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

        if(!$checkoutSession) {
            $stripeCustomer = $this->getStripeCustomer($account);

            //  Here this means that we dont have a checkout session. We need to create a checkout session with predefined Membership product
            $session = $this->gateway->checkout->sessions->create([
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
                'customer'  => $stripeCustomer->id,
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
                    'approval_url'   =>  $session->url
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

        if(!$customers->data || count($customers->data) == 0) {
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
}

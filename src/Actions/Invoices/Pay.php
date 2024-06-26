<?php

namespace NextDeveloper\Accounting\Actions\Invoices;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\CreditCards;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentGatewayMessages;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Services\TransactionsService;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\Events\Services\Events;
use NextDeveloper\IAM\Database\Models\Accounts;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\Accounting\Database\Models\Accounts as AccountingAccount;
use Omnipay\Omnipay;

/**
 * This action tries to charge the customer for the payment, and if it is successful, it will mark the invoice as paid.
 *
 * It will use the related payment gateway and payment method to charge the customer.
 */
class Pay extends AbstractAction
{
    private $conversationId = 0;

    public const EVENTS = [
        'payment-successful:NextDeveloper\Accounting\Invoices',
        'payment-failed:NextDeveloper\Accounting\Invoices'
    ];

    public const PARAMETERS = [
        'installment' => [
            'type'          =>  'integer',
            'validation'    =>  'nullable|integer'
        ]
    ];

    /**
     * @param Invoices $invoice
     */
    public function __construct(Invoices $invoice, $params = null)
    {
        $this->model = $invoice;

        $this->conversationId = Carbon::now()->timestamp;

        $this->validateParameters(self::PARAMETERS, $params);
    }

    public function handle()
    {
        $this->setProgress(0, 'Starting to pay the invoice');

        $invoice = $this->model;

        /**
         * 1) We will take the invoice and the customer related to that
         * 2) We will take the default payment method for the provider of the customer and check if the provider
         *   has a payment gateway for that payment method.
         * 3) If we have a payment gateway, we will try to charge the customer.
         *
         * 4) If we cannot charge the customer we need to write the invoice an error and mark it as failed.
         */

        if ($invoice->is_paid) {
            $this->setProgress(100, 'The invoice has already been paid');
            return;
        }

        $this->setProgress(10, 'Checking the customer country and which payment gateway to use');

        $accountingAccount = AccountingAccount::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();

        $customer = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $accountingAccount->iam_account_id)
            ->first();

        $language = Languages::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $customer->common_language_id)
            ->first();

        $accountManager = Users::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $customer->iam_user_id)
            ->first();

        if (!$language) {
            $language = Languages::withoutGlobalScope(AuthorizationScope::class)
                ->where('code', App::getLocale())
                ->first();
        }

        $this->setProgress(20, 'Choosing appropriate payment gateway for the customer');

        //  Here we are finding the payment gateway by using the country of the customer
        //  and the provider (who creates the invoice) of the customer. Because in multiple accounting system
        //  There can be more than one payment gateways and we may need to charge customers from that payment gateways
        $gateway = PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('common_country_id', $customer->common_country_id)
            ->where('iam_account_id', $invoice->iam_account_id)
            ->where('is_active', true)
            ->first();

        if (!$gateway) {
            $this->setFinishedWithError('The payment gateway is not available');
            return;
        }

        if (!class_exists($gateway->gateway)) {
            $this->setFinishedWithError('The payment gateway class does not exist');
            return;
        }

        $this->setProgress(50, 'Building the payment request for payment processor.');

        //  We are creating the gateway here
        $omnipay = Omnipay::create($gateway->gateway);
        $omnipay->setApiId($gateway->parameters['apiKey']);
        $omnipay->setSecretKey($gateway->parameters['apiSecret']);
        $omnipay->set3dSecure(false);
        $omnipay->setOrderId($invoice->uuid);
        $omnipay->setLocale($language->code);

        if ($gateway->parameters['isTest']) {
            $omnipay->setTestMode(true);
        }

        $creditCard = CreditCards::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $customer->id)
            ->where('iam_user_id', $customer->iam_user_id)
            //  We will use the default credit card for the customer but we will use the latest one first.
            //->where('is_default', true)
            ->orderBy('id', 'desc')
            ->first();

        if(!$creditCard) {
            $this->setFinishedWithError('The credit card is not available for the customer');
            return;
        }

        $cardOwner = Users::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $creditCard->iam_user_id)
            ->first();

        $cardData = [
            'firstName' => $cardOwner->name,
            'lastName' => $cardOwner->surname,
            'number' => Str::remove(' ', $creditCard->cc_number),
            'expiryMonth' => $creditCard->cc_month,
            'expiryYear' => $creditCard->cc_year,
            'cvv' => $creditCard->cc_cvv,
            'email' => $accountManager->email
        ];

        $currency = Currencies::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->common_currency_id)
            ->first();

        if (!$currency) {
            $this->setFinishedWithError('The currency that we try to charge is not available in database');
            return;
        }

        $purchaseData = [
            'identityNumber'    =>  $cardOwner->nin,
            'amount' => round($invoice->amount, 2, PHP_ROUND_HALF_UP),
            'currency' => $currency->code,
            //  This goes to the Omnipay Card data
            'card' => $cardData,
            //  Conversation ID is a timestamp, we will use this to store transaction data
            'conversationId' => $this->conversationId,
            'items' => [
                [
                    'id'    =>  1,
                    'category1' =>  'Cloud Service',
                    'itemType'  =>  'VIRTUAL',
                    'name' => $invoice->uuid,
                    'price' => $invoice->amount,
                    'quantity' => 1
                ]
            ]
        ];

        $this->setProgress(75, 'Sending the payment request.');

        //  We are giving the parameters here. These parameters are the parameters that the payment gateway needs
        $response = $omnipay->purchase($purchaseData)->send();

        $transactionLog = TransactionsService::create([
            'accounting_invoice_id'         =>  $invoice->id,
            'amount'                        => $invoice->amount,
            'common_currency_id'            => $invoice->common_currency_id,
            'accounting_payment_gateway_id' =>  $gateway->id,
            'iam_account_id'                => $invoice->iam_account_id,
            'accounting_account_id'         => $invoice->accounting_account_id,
            'conversation_identifier'       => $this->conversationId,
        ]);

        Events::fire('created:NextDeveloper\Accounting\Transactions', $transactionLog);

        //  Registering the received message here, because we may want to tell customer a reasonable error message
        try {
            if(array_key_exists('error', $response)) {
                PaymentGatewayMessages::create([
                    'accounting_payment_gateway_id' => $gateway->id,
                    'message_identifier' => $response['error']['code'],
                    'message' => $response['error']['message']
                ]);
            }
        } catch (\Exception $e) {
            //  We are not going to do anything here.
        }

        if(!$response['isSuccessful']) {
            $this->setFinishedWithError('The payment request has failed. The error message is: '
                . $response['error']['message']);

            $transactionLog->update([
                'gateway_response'  =>  $response['error']['message'],
                'is_pending' => false
            ]);

            Events::fire('payment-failed:NextDeveloper\Accounting\Invoices', $invoice);

            return;
        }

        $transactionLog->update([
            'gateway_response'  =>  'The payment has successfully processed.',
            'is_pending' => false
        ]);

        $this->setFinished('The payment request has been successfully sent.');

        $invoice->update(['is_paid' => true]);
        $invoice = $invoice->fresh();

        Events::fire('payment-successful:NextDeveloper\Accounting\Invoices', $invoice);
    }
}

<?php

namespace NextDeveloper\Accounting\Actions\Invoices;

use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Accounting\Services\TransactionsService;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Commons\Database\Models\Languages;
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

    /**
     * @param Invoices $invoice
     */
    public function __construct(Invoices $invoice, ...$args)
    {
        $this->model = $invoice;

        $this->conversationId = Carbon::now()->timestamp;
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

        $cardData = [
            'firstName' => $accountManager->name,
            'lastName' => $accountManager->surname,
            'number' => '5528790000000008',
            'expiryMonth' => '12',
            'expiryYear' => '2030',
            'cvv' => '123',
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
            'identityNumber'    =>  $accountManager->nin,
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
            'gateway_response'              => json_encode($response),
            'conversation_identifier'       => $this->conversationId,
        ]);

        if(!$response['isSuccessful']) {
            $this->setFinishedWithError('The payment request has failed. The error message is: '
                . $response['error']['message']);

            return;
        }

        $this->setFinished('The payment request has been successfully sent.');

        //  @todo: Here we need to check the response and update the invoice status
    }
}

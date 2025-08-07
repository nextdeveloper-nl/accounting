<?php

namespace NextDeveloper\Accounting\Actions\Invoices;

use Carbon\Carbon;
use Helpers\InvoiceHelper;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts as AccountingAccount;
use NextDeveloper\Accounting\Database\Models\CreditCards;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentGatewayMessages;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use Nextdeveloper\Accounting\PaymentGateways\Stripe;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Database\Models\Addresses;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Commons\Database\Models\Languages;
use NextDeveloper\Commons\Exceptions\ModelNotFoundException;
use NextDeveloper\Commons\Helpers\CountryHelper;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\Commons\Helpers\StateHelper;
use NextDeveloper\Events\Services\Events;
use NextDeveloper\IAM\Database\Models\Accounts;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
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

    private $accountManager;

    private $accountingAccount;

    private $paymentGateway;

    private $customer;

    private $language;


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

        $this->accountingAccount = AccountingAccount::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();

        AccountingHelper::fixDistributorId($this->accountingAccount);

        $this->customer = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $this->accountingAccount->iam_account_id)
            ->first();

        $this->language = Languages::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $this->customer->common_language_id)
            ->first();

        $this->accountManager = Users::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $this->customer->iam_user_id)
            ->first();

        if (!$this->language) {
            $this->language = Languages::withoutGlobalScope(AuthorizationScope::class)
                ->where('code', App::getLocale())
                ->first();
        }

        $this->setProgress(20, 'Choosing appropriate payment gateway for the customer');

        //  Here we are finding the payment gateway by using the country of the customer
        //  and the provider (who creates the invoice) of the customer. Because in multiple accounting system
        //  There can be more than one payment gateways and we may need to charge customers from that payment gateways
        $this->paymentGateway = PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('common_country_id', $this->customer->common_country_id)
            ->where('iam_account_id', $invoice->iam_account_id)
            ->where('is_active', true)
            ->first();

        Log::info(__METHOD__ . '| Payment gateway for the customer is: ' . ($this->paymentGateway ? $this->paymentGateway->name : 'not found'));

        switch ($this->paymentGateway->name) {
            case 'iyzico':
                $this->setProgress(30, 'Payment gateway is Iyzico. We will use Iyzico payment gateway to charge the customer.');
                $this->payWithIyzico();
                break;
            case 'stripe-usa':
                $this->setProgress(30, 'Payment gateway is Stripe USA. We will use Stripe USA payment gateway to charge the customer.');
                $this->payWithStripe();
                break;
        }

        Events::fire('payment-successful:NextDeveloper\Accounting\Invoices', $invoice);
    }

    public function payWithStripe()
    {
        trigger_error('The Stripe payment gateway is not implemented yet. Please use Iyzico payment gateway instead.', E_USER_WARNING);

        $invoice = $this->model;

        //  Here we are converting the invoice amount to the currency that we are going to charge
        if($this->model->common_currency_id != $this->paymentGateway->common_currency_id) {
            Log::info(__METHOD__ . '| Seems like the invoice amount and the gateway currency is different. We need to convert the amount to the gateway currency.');
            $exchangeRate = ExchangeRateHelper::getLatestRateById($this->model->common_currency_id, $this->paymentGateway->common_currency_id);

            Log::info(__METHOD__ . '| Exchange rate from ' . $this->model->common_currency_id . ' to ' . $this->paymentGateway->common_currency_id . ' is: ' . $exchangeRate);

            //  We are putting VAT here because the owner of this orchestrator may have multiple partners in various different countries.
            //  Therefor we should be able to manage the VAT according to the country of the customer.
            $this->model->updateQuietly([
                'exchange_rate' =>  $exchangeRate
            ]);

            $this->model->refresh();
        }

        $this->model->updateQuietly([
            'vat'   =>  $this->paymentGateway->vat_rate * $this->model->amount
        ]);

        $this->model->refresh();

        if (!$this->paymentGateway) {
            $this->setFinishedWithError('The payment gateway is not available');
            return;
        }

        if (!class_exists($this->paymentGateway->gateway)) {
            $this->setFinishedWithError('The payment gateway class does not exist');
            return;
        }

        $this->setProgress(50, 'Building the payment request for payment processor.');

        $gateway = new Stripe($this->paymentGateway);

        dd($gateway);
    }

    public function payWithIyzico() {
        $invoice = $this->model;

        //  Here we are converting the invoice amount to the currency that we are going to charge
        $invoice = InvoiceHelper::calculateByGateway($invoice);

        if (!$this->paymentGateway) {
            $this->setFinishedWithError('The payment gateway is not available');
            return;
        }

        if (!class_exists($this->paymentGateway->gateway)) {
            $this->setFinishedWithError('The payment gateway class does not exist');
            return;
        }

        $this->setProgress(50, 'Building the payment request for payment processor.');

        //  We are creating the gateway here
        $omnipay = Omnipay::create($this->paymentGateway->gateway);

        if($this->paymentGateway->parameters['is_test']) {
            $omnipay->setApiId($this->paymentGateway->parameters['test_api_key']);
            $omnipay->setSecretKey($this->paymentGateway->parameters['test_api_secret']);
        } else {
            $omnipay->setApiId($this->paymentGateway->parameters['live_api_key']);
            $omnipay->setSecretKey($this->paymentGateway->parameters['live_api_secret']);
        }

        $omnipay->set3dSecure(false);
        $omnipay->setOrderId($this->model->uuid);
        $omnipay->setLocale($this->language->code);

        if ($this->paymentGateway->parameters['is_test']) {
            $omnipay->setTestMode(true);
        } else {
            $omnipay->setTestMode(false);
        }

        $creditCard = CreditCards::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $this->customer->id)
            ->where('iam_user_id', $this->customer->iam_user_id)
            //  We will use the default credit card for the customer but we will use the latest one first.
            //->where('is_default', true)
            ->orderBy('id', 'desc')
            ->first();

        if(!$creditCard) {
            StateHelper::setState($this->model, 'payment-error', 'no-credit-card', null, 'There is no credit card available for the customer. Please add a credit card to this account.');

            $this->setFinishedWithError('The credit card is not available for the customer');
            return;
        }

        $cardOwner = Users::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $creditCard->iam_user_id)
            ->first();

        $accountBilled = AccountingHelper::getIamAccountFromInvoice($this->model);

        if($accountBilled->common_country_id != $this->paymentGateway->common_country_id) {
            StateHelper::setState($this->model, 'payment-error', 'country-not-supported', null, 'The payment gateway does not support the country of the customer. Please use a different payment gateway.');
            $this->setFinishedWithError('The payment gateway does not support the country of the customer');
            return;
        }

        $address = Addresses::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_id', $accountBilled->id)
            ->where('object_type', 'NextDeveloper\\IAM\\Database\\Models\\Accounts')
            ->where('is_invoice_address', true)
            ->first();

        if(!$address) {
            StateHelper::setState($this->accountingAccount, 'invoice-address', 'The invoice address is not set for the customer. Please set the invoice address for the customer.');

            throw new ModelNotFoundException('Cannot find the invoice address. Please set the invoice address for the customer.');
            return;
        }

        $cardData = [
            'firstName' => $cardOwner->name,
            'lastName' => $cardOwner->surname,
            'number' => Str::remove(' ', decrypt($creditCard->cc_number)),
            'expiryMonth' => $creditCard->cc_month,
            'expiryYear' => $creditCard->cc_year,
            'cvv' => $creditCard->cc_cvv,
            'email' => $cardOwner->email,

            //  We need to fix this section
            'billingAddress1'   =>  $address->line1,
            'billingAddress2'   =>  $address->line2,
            'billingCity'       =>  $address->city,
            'billingCountry'    =>  CountryHelper::getCountry($address)?->code ?? 'Türkiye',
            'company'           =>  $accountBilled->name
        ];

        $currency = Currencies::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->common_currency_id)
            ->first();

        if (!$currency) {
            $this->setFinishedWithError('The currency that we try to charge is not available in database');
            return;
        }

        $calculatedPrice = $this->model->amount;

        if($this->model->common_currency_id == 1) {
            $this->model->exchange_rate = 1;
            $this->model->save();
        }

        if($this->model->exchange_rate) {
            $calculatedPrice *= $this->model->exchange_rate;
        }

        if($this->model->vat) {
            $calculatedPrice += ( $this->model->vat * $this->model->exchange_rate );
        }

        $purchaseData = [
            'identityNumber'    =>  $cardOwner->nin,
            'amount' => round($calculatedPrice, 2, PHP_ROUND_HALF_UP),
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
                    'price' => round($calculatedPrice, 2, PHP_ROUND_HALF_UP),
                    'quantity' => 1
                ]
            ]
        ];

        $this->setProgress(75, 'Sending the payment request.');

        //  We are giving the parameters here. These parameters are the parameters that the payment gateway needs
        try {
            $response = $omnipay->purchase($purchaseData)->send();
        } catch (\Exception $e) {
            //  We don't catch the payment exceptions here. Payment exceptions are handled down below

            $transaction = new Transactions();
            $transaction->unsetEventDispatcher();

            $transactionLog = $transaction->create([
                'accounting_invoice_id'         =>  $invoice->id,
                'amount'                        => $calculatedPrice,
                'common_currency_id'            => $invoice->common_currency_id,
                'accounting_payment_gateway_id' =>  $this->paymentGateway->id,
                'iam_account_id'                => $invoice->iam_account_id,
                'accounting_account_id'         => $invoice->accounting_account_id,
                'conversation_identifier'       => $this->conversationId,
                'is_pending' => true,
                'gateway_response' => $e->getMessage()
            ]);

            $this->setFinishedWithError('The payment request has failed. The error message is: '
                . $e->getMessage());

            StateHelper::setState($this->model, 'payment-error', 'payment-processor-error', StateHelper::STATE_WARNING, $e->getMessage());

            return;
        }

        $transaction = new Transactions();
        $transaction->unsetEventDispatcher();

        $transactionLog = $transaction->create([
            'accounting_invoice_id'         =>  $invoice->id,
            'amount'                        => $calculatedPrice,
            'common_currency_id'            => $invoice->common_currency_id,
            'accounting_payment_gateway_id' =>  $this->paymentGateway->id,
            'iam_account_id'                => $invoice->iam_account_id,
            'accounting_account_id'         => $invoice->accounting_account_id,
            'conversation_identifier'       => $this->conversationId,
            'is_pending' => true
        ]);

        Events::fire('created:NextDeveloper\Accounting\Transactions', $transactionLog);

        //  Registering the received message here, because we may want to tell customer a reasonable error message
        try {
            if($response['isSuccessful'] === false) {
                PaymentGatewayMessages::create([
                    'accounting_payment_gateway_id' => $this->paymentGateway->id,
                    'message_identifier' => $response['error']['code'],
                    'message' => $response['error']['message']
                ]);

                Log::error(__METHOD__ . '| Error response: ' . $response['error']['message']);

                $this->setFinishedWithError('The payment request has failed. The error message is: '
                    . $response['error']['message']);
            }
        } catch (\Exception $e) {
            //  We are not going to do anything here.
        }

        if(!$response['isSuccessful']) {
            $errorCode = intval($response['error']['code']);
            //  Here we get the payment exceptions
            switch ($errorCode) {
                case 1:
                    StateHelper::setState($creditCard, 'card-number', 'payment-processor-error', null,'There is a system error in payment processor.');
                    break;
                case 13:
                    StateHelper::setState($creditCard, 'card-number', 'expiration-date-invalid', null, 'Expiration date is invalid. Card owner should provide a valid expiration date.');
                    break;
                case 14:
                    StateHelper::setState($creditCard, 'card-number', 'expiration-year-invalid', null, 'Expiration year is invalid. Card owner should provide a valid expiration year.');
                    break;
                case 10054:
                    StateHelper::setState($creditCard, 'card-number', 'expired', null, 'Card is expired. Card owner should provide a valid card with valid expiration date.');
                    break;
                case 12:
                    StateHelper::setState($creditCard, 'card-number', 'card-number-invalid', null, 'Card number is invalid. Card owner should provide a valid card number.');
                    StateHelper::setState($invoice, 'payment-error', 'card-number-invalid', null, 'Credit card has invalid number. Card owner should provide a valid card number.');
                    break;
                case 5008:
                    StateHelper::setState($invoice, 'payment-error', 'card-number-invalid', null, 'The amount is more than 100.000 TL, therefore we cannot process the payment. Please reduce the number.');
                    break;
                case 10051:
                    StateHelper::setState($creditCard, 'fund', 'not-enough-funds', null, 'There is not enough fund in the card. Card owner should provide a valid card with enough fund.');
                    StateHelper::setState($invoice, 'payment-error', 'not-enough-funds', null,'There is not enough fund in the card. Card owner should provide a valid card with enough fund.');
                    break;
                case 10005:
                    StateHelper::setState($creditCard, 'payment-declined', 'declined-by-issuer', null, 'Payment is declined by the card issuer. Please talk to your bank.');
                    StateHelper::setState($invoice, 'payment-error', 'declined-by-issuer', null, 'Payment is declined by the card issuer. Please talk to your bank.');
                    break;
                case 10012:
                    StateHelper::setState($creditCard, 'invalid-transaction', 'invalid-transcation', null, 'There is an invalid transaction. Please try again later.');
                    StateHelper::setState($invoice, 'payment-error', 'invalid-transaction', null, 'There is an invalid transaction. Please try again later.');
                    break;
                case 6001:
                    StateHelper::setState($creditCard, 'card-status', 'lost', null, 'Card seems to be lost or stolen, please provide a valid card or consult to your bank.');
                    StateHelper::setState($invoice, 'payment-error', 'lost-card', null, 'Card seems to be lost or stolen, please provide a valid card or consult to your bank.');
                    break;
                case 10034:
                    StateHelper::setState($creditCard, 'card-status', 'possible-fraud', null, 'This card seems to used in a fraud. Please provide a valid credit card.');
                    StateHelper::setState($invoice, 'payment-error', 'lost-card', null, 'This card seems to used in a fraud. Please provide a valid credit card.');
                    break;
                default:
                    StateHelper::setState($creditCard, 'error', $response['error']['message']);
                    StateHelper::setState($invoice, 'payment-error', 'Card error: ' . $response['error']['message']);
            }

            $this->setFinishedWithError('The payment request has failed. The error message is: '
                . $response['error']['message']);

            $transactionLog->update([
                'gateway_response'  =>  $response['error']['message'],
                'is_pending' => false
            ]);

            Events::fire('payment-failed:NextDeveloper\Accounting\Invoices', $invoice);

            return;
        }

        StateHelper::clearStates($this->model);

        $transactionLog->update([
            'gateway_response'  =>  'The payment has successfully processed.',
            'is_pending' => false
        ]);

        $this->setFinished('The payment request has been successfully sent.');

        $invoice->update(['is_paid' => true]);
        $invoice = $invoice->fresh();

        StateHelper::deleteState($invoice, 'payment-error');

        $creditCard->update([
            'is_valid'      =>  true,
            'is_default'    =>  true
        ]);
    }
}

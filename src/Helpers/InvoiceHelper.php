<?php

namespace Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Exceptions\AccountingException;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\Accounting\Services\InvoicesService;
use NextDeveloper\Commons\Database\GlobalScopes\LimitScope;
use NextDeveloper\Commons\Database\Models\Countries;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\Commons\Services\CurrenciesService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\IAM\Services\AccountsService;

class InvoiceHelper
{
    public static function fixCurrencyCode(Invoices $invoice) : Invoices
    {
        //  This is a helper to fix the currency code of the invoice.
        //  We are doing this because we are using the common_currency_id in the invoices table.
        //  But we need to use the currency code in the invoice items table.
        $account = self::getAccount($invoice);
        $distributor = AccountingHelper::getDistributorAccount($account);

        if(!$distributor->common_currency_id) {
            throw new AccountingException(
                'Distributor account does not have a common currency ID. Please set it up in the distributor account.'
            );
        }

        $invoice->updateQuietly([
            'common_currency_id' => $distributor->common_currency_id
        ]);

        return $invoice;
    }

    public static function hasInvoice(Accounts $account) : bool
    {
        $invoices = Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $account->id)
            ->where('is_sealed', false)
            ->get();

        if($invoices->isEmpty())
            return false;

        return true;
    }

    public static function getAccount(Invoices $invoice)
    {
        return Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();
    }

    public static function getMyInvoices() : Collection
    {
        return Invoices::orderBy('id', 'desc')->get();
    }

    public static function getMyUnpaidInvoices() : Collection
    {
        return Invoices::where('is_paid', false)->orderBy('id', 'desc')->get();
    }

    /**
     * This helper will return latest open invoice. If there is no open invoice, it will create a new one.
     *
     * @return Invoices
     */
    public static function getInvoice(Accounts $accounts, $year = null, $month = null) : Invoices
    {
        $invoice = Invoices::where('is_sealed', false)
            ->where('accounting_account_id', $accounts->id);

        if(!$year) {
            $year = Carbon::now()->year;
            $month = Carbon::now()->month;
        }

        $invoice = $invoice->where([
            'term_year'     =>  $year,
            'term_month'    =>  $month
        ]);

        $invoice = $invoice->latest()->first();

        if(!$invoice) {
            Log::info(__METHOD__ . ' | Creating a new invoice for account: ' . $accounts->id
                . ' for year: ' . $year . ' month: ' . $month);

            $invoice = InvoicesService::create([
                'status'    => 'open',
                'accounting_account_id' =>  $accounts->id,
                'amount'    => 0,
                'common_currency_id'    =>  CurrenciesService::getDefaultCurrency()->id,
                'vat'       => 0,
                'term_year'     =>  $year,
                'term_month'    =>  $month,
                //  We don't put due date here because due date is +7 days after we close the invoice
                //'due_date'  => $from->copy()->addMonth()->addDays(7)
                'iam_account_id'    =>  UserHelper::currentAccount()->id,
                'iam_user_id'       =>  UserHelper::me()->id
            ]);
        }

        $distributor = AccountingHelper::getDistributorAccount($accounts);

        if(!$distributor) {
            AccountingHelper::fixDistributorId(
                Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', $invoice->accounting_account_id)
                    ->first()
            );

            $distributor = AccountingHelper::getDistributorAccount($accounts);
        }

        $paymentGateway = AccountingHelper::getPaymentGatewayOfDistributor($distributor);

        if($paymentGateway) {
            //  If the gateway has stripe in its name, we need to create the checkout session for the invoice.
            if(Str::contains($paymentGateway->name, 'stripe')) {
//                $stripe = new Stripe($paymentGateway);
//                $checkoutSession = $stripe->getCheckoutSession($accounts);
//
//                dd($checkoutSession);
            }
        }

        return $invoice->fresh();
    }

    public static function updateInvoiceAmount(Invoices $invoice)
    {
        $items = InvoiceItems::withoutGlobalScope(AuthorizationScope::class)
            ->withoutGlobalScope(LimitScope::class)
            ->where('accounting_invoice_id', $invoice->id)
            ->get();

        $amounts = [];

        foreach($items as $item) {
            $itemTotalPrice = $item->total_price;

            if(array_key_exists($item->common_currency_id, $amounts))
                $amounts[$item->common_currency_id] += $itemTotalPrice;
            else
                $amounts[$item->common_currency_id] = $itemTotalPrice;
        }

        //  Now we are finding the provider, from there we will find the invoice amount
        $provider = AccountingHelper::getCustomerProvider(
            InvoiceHelper::getAccount($invoice)
        );

        if(!$provider) {
            Log::info(__METHOD__ . '| Cannot find the provider for the account: ' . InvoiceHelper::getAccount($invoice)->id);
            throw new AccountingException('Cannot find the provider for the account: ' . InvoiceHelper::getAccount($invoice)->id);
        }

        $providerAccountingAccount = AccountingHelper::getAccount($provider);

        $providerCurrency = ExchangeRateHelper::getCurrencyFromId($providerAccountingAccount->common_currency_id);

        Log::info(__METHOD__ . '| Provider currency: ' . $providerCurrency->code);

        $currencyCodes = array_keys($amounts);

        $totalAmount = 0;

        foreach ($currencyCodes as $code) {
            $amountCurrency = ExchangeRateHelper::getCurrencyFromId($code);

            Log::info(__METHOD__ . '| Invoice (' . $invoice->uuid . ') has ' . $amounts[$code] . $amountCurrency->code);

            if($amountCurrency->id != $providerCurrency->id)
                $convertedAmount = ExchangeRateHelper::convert(
                    fromCurrencyCode: $amountCurrency->code,
                    toCurrencyCode: $providerCurrency->code,
                    amount: $amounts[$code]
                );
            else
                $convertedAmount = $amounts[$code];

            Log::info(__METHOD__ . '| Invoice (' . $invoice->uuid . ') converted from '
                . $amounts[$code] . $amountCurrency->code . ' to ' . $convertedAmount . $providerCurrency->code);

            $totalAmount += $convertedAmount;
        }

        Log::info(__METHOD__ . '| The total amount for invoice (' . $invoice->uuid . ') is: ' . $totalAmount . $providerCurrency->code);

        $invoice->update([
            'amount'                =>  $totalAmount,
            'common_currency_id'    =>  $providerCurrency->id
        ]);
    }

    public static function getInvoiceById($id)
    {
        if(Str::isUuid($id)) {
            return Invoices::withoutGlobalScope(AuthorizationScope::class)
                ->where('uuid', $id)
                ->first();
        }

        return Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $id)
            ->first();
    }

    public static function getGatewayForInvoice(Invoices $invoice) : ?PaymentGateways
    {
        $invoiceOwnerAccountingAccount = self::getAccount($invoice);
        $account = AccountingHelper::getIamAccount($invoiceOwnerAccountingAccount);

        if(!$account->common_country_id) {
            $account = AccountsService::fixCommonCountryId($account);
        }

        return PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('common_country_id', $account->common_country_id)
            ->where('is_active', true)
            ->first();
    }

    public static function calculateByGateway(Invoices $invoice) : Invoices
    {
        $gateway = self::getGatewayForInvoice($invoice);

        if($invoice->common_currency_id != $gateway->common_currency_id) {
            Log::info(__METHOD__ . '| Seems like the invoice amount and the gateway currency is different. We need to convert the amount to the gateway currency.');
            $exchangeRate = ExchangeRateHelper::getLatestRateById($invoice->common_currency_id, $gateway->common_currency_id);

            Log::info(__METHOD__ . '| Exchange rate from ' . $invoice->common_currency_id . ' to ' . $gateway->common_currency_id . ' is: ' . $exchangeRate);

            $invoice->updateQuietly([
                'exchange_rate' =>  $exchangeRate
            ]);

            $invoice->refresh();
        }

        //  We are putting VAT here because the owner of this orchestrator may have multiple partners in various different countries.
        //  Therefor we should be able to manage the VAT according to the country of the customer.
        $invoice->updateQuietly([
            'vat'   =>  $gateway->vat_rate * $invoice->amount
        ]);

        return $invoice;
    }


    /**
     * Returns the commission invoice by parent invoice
     *
     * @param Invoices $parentInvoice
     * @return Invoices|null
     */
    public static function getCommissionInvoiceByParentInvoice(Invoices $parentInvoice) : ?Invoices
    {
        return Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->where('parent_invoice_id', $parentInvoice->id)
            ->where('is_commission_invoice', true)
            ->first();
    }
}

<?php

namespace Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Services\InvoicesService;
use NextDeveloper\Commons\Database\GlobalScopes\LimitScope;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\Commons\Services\CurrenciesService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class InvoiceHelper
{
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
            $unitPrice = $item->unit_price * $item->quantity;

            if(array_key_exists($item->common_currency_id, $amounts))
                $amounts[$item->common_currency_id] += $unitPrice;
            else
                $amounts[$item->common_currency_id] = $unitPrice;
        }

        //  Now we are finding the provider, from there we will find the invoice amount
        $provider = AccountingHelper::getCustomerProvider(
            InvoiceHelper::getAccount($invoice)
        );

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
}

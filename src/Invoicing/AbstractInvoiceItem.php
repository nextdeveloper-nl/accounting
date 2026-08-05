<?php

namespace NextDeveloper\Accounting\Invoicing;

use App\Helpers\LeoAccountsHelper;
use Carbon\Carbon;
use Helpers\InvoiceHelper;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\Accounting\Helpers\ContractHelper;
use NextDeveloper\Accounting\Services\InvoiceItemsService;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\Commons\Services\CurrenciesService;
use NextDeveloper\Events\Services\Events;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 *
 */
abstract class AbstractInvoiceItem
{
    protected $model;

    private $invoice;

    protected $from;

    protected $to;

    private $year;

    private $month;

    public function __construct($model, $year, $month)
    {
        $this->model = $model;

        $this->year = $year;
        $this->month = $month;

        $this->from = Carbon::createFromDate($year, $month)->setTimezone('GMT')->startOfMonth();
        $this->to = $this->from->copy()->endOfMonth();
    }

    /**
     * An object that is under contract for the whole term is not invoiced at all, so the
     * invoice is only reached for when there is really something to bill. Otherwise a
     * fully contracted customer would still get an empty invoice raised every month.
     */
    private function getInvoice()
    {
        if(!$this->invoice) {
            $this->invoice = InvoiceHelper::getInvoice(
                $this->getAccountingAccount(),
                $this->year,
                $this->month
            );
        }

        return $this->invoice;
    }

    private function getAccountingAccount()
    {
        return AccountingHelper::getAccount(
            UserHelper::getAccountById( $this->model->iam_account_id )
        );
    }

    /**
     * The invoice of this term if the customer already has one. Unlike getInvoice() this
     * never creates one.
     */
    private function findInvoice()
    {
        return Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $this->getAccountingAccount()->id)
            ->where('term_year', $this->year)
            ->where('term_month', $this->month)
            ->latest()
            ->first();
    }

    protected function getItemContract()
    {
        return ContractHelper::getContractItemForWindow($this->model, $this->from, $this->to);
    }

    protected function setItemCost($cost, Currencies $currency, $details = [], $contractItem = null) {
        $contract = $this->getItemContract();
        $coverage = $contract ? ContractHelper::getCoverageRatio($contract, $this->from, $this->to) : 0;

        /**
         * The contract of this term is paid with the contract invoice, not month by
         * month. A term that covers the whole month leaves nothing to invoice here.
         */
        if($coverage >= 1) {
            return $this->settleCoveredItem($contract);
        }

        $invoice = $this->getInvoice();

        $item = InvoiceItems::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_type', get_class($this->model))
            ->where('object_id', $this->model->id)
            ->where('accounting_invoice_id', $invoice->id)
            ->first();

        if(!$item) {
            $item = InvoiceItemsService::create([
                'accounting_invoice_id' =>  $invoice->id,
                'object_type'   =>  is_object($this->model) ? get_class($this->model) : $this->model,
                'object_id'     =>  is_object($this->model) ? $this->model->id : 0,
                'quantity'      =>  1,
                'unit_price'    =>  $cost,
                'common_currency_id'    =>  $currency->id,
                'accounting_account_id' =>  $invoice->accounting_account_id
            ]);
        }

        $item->update([
            'unit_price'    =>  $cost
        ]);

        $this->applyContract($item, $contract, $coverage);
        $this->convertToLocalCurrency($item);

        Events::fire('updated:NextDeveloper\Accounting\InvoiceItems', $item);

        //  We are removing this from here because it is creating almost infinite loop.
        //  We will be calculating invoice amounts, every hour, or just before the customer wants to pay it
        //  We will be doing this calculation at the database
        InvoiceHelper::updateInvoiceAmount($invoice);

        return $item;
    }

    /**
     * The object is under contract for this whole term. If it was already invoiced
     * before the contract was signed, that line is dropped to zero; otherwise there is
     * nothing to do and no invoice is created.
     */
    private function settleCoveredItem($contract)
    {
        $invoice = $this->findInvoice();

        if(!$invoice) {
            Log::info('[##COVERED BY CONTRACT##] ' . get_class($this->model) . ' ' . $this->model->id
                . ' is covered by the contract ' . $contract->uuid . ' for the whole term, so it is not invoiced.');

            return null;
        }

        $item = InvoiceItems::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_type', get_class($this->model))
            ->where('object_id', $this->model->id)
            ->where('accounting_invoice_id', $invoice->id)
            ->first();

        if(!$item) {
            return null;
        }

        ContractHelper::settleInvoiceItem($item, $contract, 1);

        InvoiceHelper::updateInvoiceAmount($invoice);

        return $item;
    }

    /**
     * A contract is paid with its own invoice, for the whole of its term, so the part of
     * this term that the contract covers is not billed here a second time. Only the part
     * of the term that falls outside the contract stays on this invoice.
     *
     * A term that is fully covered never reaches this point: setItemCost() stops before
     * an invoice is even opened.
     */
    protected function applyContract(InvoiceItems $item, $contract = null, $coverage = null)
    {
        $contract = $contract ?? $this->getItemContract();

        if(!$contract) {
            return $item;
        }

        $coverage = $coverage ?? ContractHelper::getCoverageRatio($contract, $this->from, $this->to);

        if($coverage <= 0) {
            return $item;
        }

        ContractHelper::settleInvoiceItem($item, $contract, $coverage);

        return $item;
    }

    protected function convertToLocalCurrency($item)
    {
        //  Now we are finding the provider, from there we will find the invoice amount
        $provider = AccountingHelper::getCustomerProvider(
            InvoiceHelper::getAccount($this->getInvoice())
        );

        if(!$provider) {
            Log::info(__METHOD__ . ' | Cannot find the provider for the account: ' . InvoiceHelper::getAccount($this->getInvoice())->id);
            throw new \Exception('Cannot find the provider for the account: ' . InvoiceHelper::getAccount($this->getInvoice())->id);
        }

        $providerAccountingAccount = AccountingHelper::getAccount($provider);

        $providerCurrency = ExchangeRateHelper::getCurrencyFromId($providerAccountingAccount->common_currency_id);

        Log::info(__METHOD__ . ' | Trying to convert the unit price of invoice item: ' . $item->uuid);

        $itemCurrency = ExchangeRateHelper::getCurrencyFromId($item->common_currency_id);

        $unitPrice = ExchangeRateHelper::convert(
            fromCurrencyCode: $itemCurrency->code,
            toCurrencyCode: $providerCurrency->code,
            amount: $item->total_price
        );

        $item->updateQuietly([
            'details'   =>  array_merge($item->details ?? [], [
                'amount_before'    => $item->unit_price * $item->quantity,
                'amount_after'     => $unitPrice,
                'from_currency' =>  $itemCurrency->code,
                'to_currency'   =>  $providerCurrency->code,
                'exchange_rate' =>  ($item->unit_price * $item->quantity) != 0 ? $unitPrice / ($item->unit_price * $item->quantity) : 0
            ])
        ]);
    }
}

<?php

namespace NextDeveloper\Accounting\Invoicing;

use Carbon\Carbon;
use Helpers\AccountingHelper;
use Helpers\InvoiceHelper;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\ContractItemsPerspective;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Services\InvoiceItemsService;
use NextDeveloper\Commons\Database\Models\Currencies;
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

    public function __construct($model, $year, $month)
    {
        $this->model = $model;

        $this->from = Carbon::createFromDate($year, $month)->setTimezone('GMT')->startOfMonth();
        $this->to = $this->from->copy()->endOfMonth();

        $this->invoice = InvoiceHelper::getInvoice(
            AccountingHelper::getAccount(
                UserHelper::getAccountById( $this->model->iam_account_id )
            ),
            $year,
            $month
        );
    }

    protected function getItemContract()
    {
        return ContractItemsPerspective::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_type', get_class($this->model))
            ->where('object_id', $this->model->id)
            ->where('term_starts', '<', Carbon::createFromDate($this->invoice->term_year, $this->invoice->term_month))
            ->where('term_ends', '>', Carbon::createFromDate($this->invoice->term_year, $this->invoice->term_month))
            ->where('is_signed', true)
            ->where('is_approved', true)
            ->first();
    }

    protected function setItemCost($cost, Currencies $currency, $details = [], $contractItem = null) {
        $item = InvoiceItems::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_type', get_class($this->model))
            ->where('object_id', $this->model->id)
            ->where('accounting_invoice_id', $this->invoice->id)
            ->first();

        if(!$item) {
            $item = InvoiceItemsService::create([
                'accounting_invoice_id' =>  $this->invoice->id,
                'object_type'   =>  is_object($this->model) ? get_class($this->model) : $this->model,
                'object_id'     =>  is_object($this->model) ? $this->model->id : 0,
                'quantity'      =>  1,
                'unit_price'    =>  $cost,
                'common_currency_id'    =>  $currency->id,
                'accounting_account_id' =>  $this->invoice->accounting_account_id
            ]);
        }

        $contract = $this->getItemContract();

        if($contract) {
            //  If we have fixed price, we are running this
            if($contract->contract_type = 'price') {
                $details[] = 'We set the price to ' . $contract->price
                    . CurrenciesService::getCurrencyById($contract->common_currency_id)->code
                    . ' because of the contract: '
                    . $contract->uuid;

                $item->update([
                    'unit_price'    =>  $contract->price,
                    'details'   =>  $details
                ]);

                if($contract->common_currency_id) {
                    $item->update(['common_currency_id'    =>  $contract->common_currency_id]);
                }

                Log::info('[##HAS FIXED PRICE DISCOUNT##] We set the price to ' . $contract->price
                    . CurrenciesService::getCurrencyById($contract->common_currency_id)->code
                    . ' because of the contract: '
                    . $contract->uuid);
            } else {
                //  If we have discount only we apply this.
                $cost = $cost * ((100 - $contract->discount_fixed) / 100);
                $details[] = 'We applied %' . $contract->discount_fixed . ' discount because of the contract: '
                    . $contract->uuid;

                Log::info('[##HAS DISCOUNT##] We applied %' . $contract->discount_fixed . ' discount because of the contract: '
                    . $contract->uuid);

                $item->update([
                    'unit_price'    =>  $cost,
                    'details'   =>  $details
                ]);
            }
        } else {
            $item->update([
                'unit_price'    =>  $cost,
                'details'   =>  $details,
                'accounting_account_id' =>  $this->invoice->accounting_account_id
            ]);
        }

        Events::fire('updated:NextDeveloper\Accounting\InvoiceItems', $item);

        InvoiceHelper::updateInvoiceAmount($this->invoice);

        return $item;
    }
}

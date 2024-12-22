<?php

namespace NextDeveloper\Accounting\Invoicing;

use Carbon\Carbon;
use Helpers\AccountingHelper;
use Helpers\InvoiceHelper;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Services\InvoiceItemsService;
use NextDeveloper\Commons\Database\Models\Currencies;
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

    protected function setItemCost($cost, Currencies $currency) {
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

        $item->update([
            'unit_price'    =>  $cost,
            'accounting_account_id' =>  $this->invoice->accounting_account_id
        ]);

        Events::fire('updated:NextDeveloper\Accounting\InvoiceItems', $item);

        InvoiceHelper::updateInvoiceAmount($this->invoice);

        return $item;
    }
}

<?php

namespace Helpers;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Services\InvoiceItemsService;
use NextDeveloper\Accounting\Services\InvoicesService;
use NextDeveloper\Commons\Database\Models\Currencies;

class InvoiceHelper
{
    /**
     * This function automatically adds an item to the invoice
     *
     * @param $item
     * @param $price
     * @param $currency
     * @param $quantity
     * @return void
     */
    public static function addItem($item, $price, Currencies $currency, $quantity = 1) : InvoiceItems
    {
        /**
         * Here we will get the last open invoice. You can use self::getInvoice() to get the latest open invoice.
         * Then we need to create an item and attach it to the invoice.
         */

        $invoice = self::getInvoice();

        $item = InvoiceItemsService::create([
            'accounting_invoice_id' =>  $invoice->id,
            'object_type'   =>  is_object($item) ? get_class($item) : $item,
            'object_id'     =>  is_object($item) ? $item->id : 0,
            'quantity'      =>  $quantity,
            'unit_price'    =>  $price,
            'common_currency_id'    =>  $currency->id
        ]);

        return $item;
    }

    /**
     * This helper will return latest open invoice. If there is no open invoice, it will create a new one.
     *
     * @return Invoices
     */
    public static function getInvoice(Accounts $accounts) : Invoices
    {
        $invoice = Invoices::where('status', 'open')
            ->where('accounting_account_id', $accounts->id)
            ->latest()->first();

        if(!$invoice) {
            $invoice = InvoicesService::create([
                'status' => 'open'
            ]);
        }

        return $invoice;
    }
}

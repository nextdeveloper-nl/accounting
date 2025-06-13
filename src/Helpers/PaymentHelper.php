<?php

namespace Helpers;

use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Database\Models\Currencies;

class PaymentHelper
{
    /**
     * This function tries to pay the invoice and if it is paid, it closes the invoice (if it is not already closed)
     *
     * @param Invoices $invoices
     * @return bool
     */
    public static function pay(Invoices $invoices) : bool
    {
        /*
         * Here we will try to pay the invoice. If the invoice is already paid, we will return true.
         */
        if($invoices->is_paid) {
            return true;
        }

        return false;
    }

    public static function get($price, $vat, Currencies $currency) : Invoices
    {
        $invoice = InvoiceHelper::getInvoice();

        $item = InvoiceHelper::addItem('Payment', $price, $currency);

        return $invoice;
    }
}

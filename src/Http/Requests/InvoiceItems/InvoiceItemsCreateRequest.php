<?php

namespace NextDeveloper\Accounting\Http\Requests\InvoiceItems;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoiceItemsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_type' => 'required|string',
        'object_id' => 'required',
        'quantity' => 'integer',
        'unit_price' => 'required',
        'common_currency_id' => 'required|exists:common_currencies,uuid|uuid',
        'accounting_invoice_id' => 'nullable|exists:accounting_invoices,uuid|uuid',
        'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'details' => 'nullable',
        'discount' => '',
        'total_price' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
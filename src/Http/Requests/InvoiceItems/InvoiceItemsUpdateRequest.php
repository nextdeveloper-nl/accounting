<?php

namespace NextDeveloper\Accounting\Http\Requests\InvoiceItems;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoiceItemsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_type' => 'nullable|string',
        'object_id' => 'nullable',
        'quantity' => 'integer',
        'unit_price' => 'nullable',
        'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
        'accounting_invoice_id' => 'nullable|exists:accounting_invoices,uuid|uuid',
        'total_price' => 'nullable',
        'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'details' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
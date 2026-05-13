<?php

namespace NextDeveloper\Accounting\Http\Requests\InvoiceItemsPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoiceItemsPerspectiveCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'accounting_invoice_id' => 'nullable|exists:accounting_invoices,uuid|uuid',
'term_year' => 'nullable|integer',
'term_month' => 'nullable|integer',
'invoice_amount' => 'nullable',
'object_type' => 'nullable|string',
'object_id' => 'nullable',
'unit_price' => 'nullable',
'quantity' => 'nullable|integer',
'total_price' => 'nullable',
'name' => 'nullable|string',
'accounting_identifier' => 'nullable|string',
'credit' => 'nullable',
'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
'common_currency_code' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
<?php

namespace NextDeveloper\Accounting\Http\Requests\Invoices;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoicesCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_account_id' => 'required|exists:accounting_accounts,uuid|uuid',
        'invoice_number' => 'required|string',
        'exchange_rate' => 'required',
        'amount' => 'required',
        'common_currency_id' => 'required|exists:common_currencies,uuid|uuid',
        'vat' => 'required',
        'is_paid' => 'boolean',
        'is_refund' => 'boolean',
        'due_date' => 'required|date',
        'gift_code_id' => 'nullable|exists:gift_codes,uuid|uuid',
        'is_payable' => 'boolean',
        'is_sealed' => 'boolean',
        'note' => 'string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
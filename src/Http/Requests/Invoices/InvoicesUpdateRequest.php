<?php

namespace NextDeveloper\Accounting\Http\Requests\Invoices;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoicesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'invoice_number' => 'nullable|string',
        'exchange_rate' => 'nullable',
        'amount' => 'nullable',
        'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
        'vat' => 'nullable',
        'is_paid' => 'boolean',
        'is_refund' => 'boolean',
        'due_date' => 'nullable|date',
        'gift_code_id' => 'nullable|exists:gift_codes,uuid|uuid',
        'is_payable' => 'boolean',
        'is_sealed' => 'boolean',
        'note' => 'string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
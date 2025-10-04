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
        'invoice_number' => 'nullable|string',
        'exchange_rate' => 'nullable',
        'amount' => 'required',
        'common_currency_id' => 'required|exists:common_currencies,uuid|uuid',
        'vat' => 'required',
        'is_paid' => 'boolean',
        'is_refund' => 'boolean',
        'due_date' => 'nullable|date',
        'is_payable' => 'boolean',
        'is_sealed' => 'boolean',
        'note' => 'string',
        'term_year' => 'nullable|integer',
        'term_month' => 'nullable|integer',
        'is_cancelled' => 'boolean',
        'cancellation_reason' => 'nullable|string',
        'payment_link_url' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
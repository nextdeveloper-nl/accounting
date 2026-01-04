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
        'is_payable' => 'boolean',
        'is_sealed' => 'boolean',
        'note' => 'string',
        'term_year' => 'nullable|integer',
        'term_month' => 'nullable|integer',
        'is_cancelled' => 'boolean',
        'cancellation_reason' => 'nullable|string',
        'payment_link_url' => 'nullable|string',
        'is_commission_invoice' => 'boolean',
        'is_commission_invoice_for_partner' => 'boolean',
        'is_commission_invoice_for_distributor' => 'boolean',
        'is_commission_invoice_for_affiliate' => 'boolean',
        'is_commission_invoice_for_sales_partner' => 'boolean',
        'parent_invoice_id' => 'nullable|exists:accounting_invoices,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
<?php

namespace NextDeveloper\Accounting\Http\Requests\Transactions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class TransactionsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_invoice_id' => 'nullable|exists:accounting_invoices,uuid|uuid',
        'amount' => 'nullable',
        'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
        'accounting_payment_gateway_id' => 'nullable|exists:accounting_payment_gateways,uuid|uuid',
        'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'gateway_response' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
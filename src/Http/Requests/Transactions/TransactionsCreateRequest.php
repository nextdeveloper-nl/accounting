<?php

namespace NextDeveloper\Accounting\Http\Requests\Transactions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class TransactionsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_invoice_id' => 'required|exists:accounting_invoices,uuid|uuid',
        'amount' => 'required',
        'common_currency_id' => 'required|exists:common_currencies,uuid|uuid',
        'accounting_payment_gateway_id' => 'required|exists:accounting_payment_gateways,uuid|uuid',
        'accounting_account_id' => 'required|exists:accounting_accounts,uuid|uuid',
        'gateway_response' => 'required|string',
        'conversation_identifier' => 'required|string',
        'is_pending' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
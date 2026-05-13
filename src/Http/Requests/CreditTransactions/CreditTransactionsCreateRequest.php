<?php

namespace NextDeveloper\Accounting\Http\Requests\CreditTransactions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class CreditTransactionsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'accounting_account_id' => 'required|exists:accounting_accounts,uuid|uuid',
'amount' => 'required',
'type' => 'required|string',
'balance_after' => 'required',
'object_type' => 'nullable|string',
'object_id' => 'nullable',
'description' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
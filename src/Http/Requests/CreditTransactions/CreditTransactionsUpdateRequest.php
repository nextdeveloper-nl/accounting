<?php

namespace NextDeveloper\Accounting\Http\Requests\CreditTransactions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class CreditTransactionsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
'amount' => 'nullable',
'type' => 'nullable|string',
'balance_after' => 'nullable',
'object_type' => 'nullable|string',
'object_id' => 'nullable',
'description' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
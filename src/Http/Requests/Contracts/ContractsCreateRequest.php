<?php

namespace NextDeveloper\Accounting\Http\Requests\Contracts;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContractsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'crm_account_id' => 'required|exists:crm_accounts,uuid|uuid',
        'name' => 'required|string',
        'description' => 'nullable|string',
        'term_starts' => 'required|date',
        'term_ends' => 'required|date',
        'contract_type' => 'string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
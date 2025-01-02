<?php

namespace NextDeveloper\Accounting\Http\Requests\Contracts;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContractsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'crm_account_id' => 'nullable|exists:crm_accounts,uuid|uuid',
        'name' => 'nullable|string',
        'description' => 'nullable|string',
        'term_starts' => 'nullable|date',
        'term_ends' => 'nullable|date',
        'contract_type' => 'string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
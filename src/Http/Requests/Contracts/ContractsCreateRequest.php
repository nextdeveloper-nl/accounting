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
            'accounting_account_id' => 'required|exists:accounting_accounts,uuid|uuid',
        'name' => 'required|string',
        'description' => 'nullable|string',
        'term_starts' => 'required|date',
        'term_ends' => 'required|date',
        'is_approved' => 'boolean',
        'is_signed' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE



}
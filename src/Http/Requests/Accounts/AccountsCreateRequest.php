<?php

namespace NextDeveloper\Accounting\Http\Requests\Accounts;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AccountsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'tax_office' => 'nullable|string',
        'tax_number' => 'nullable|string',
        'accounting_identifier' => 'nullable|string',
        'credit' => '',
        'common_currency_id' => 'required|exists:common_currencies,uuid|uuid',
        'trade_office_number' => 'nullable|string',
        'trade_office' => 'nullable|string',
        'tr_mersis' => 'nullable|string',
        'is_suspended' => 'boolean',
        'balance' => 'nullable',
        'is_disabled' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
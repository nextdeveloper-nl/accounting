<?php

namespace NextDeveloper\Accounting\Http\Requests\Accounts;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AccountsUpdateRequest extends AbstractFormRequest
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
        'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
<?php

namespace NextDeveloper\Accounting\Http\Requests\ContractItems;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContractItemsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_type' => 'nullable|string',
        'object_id' => 'nullable',
        'accounting_contract_id' => 'nullable|exists:accounting_contracts,uuid|uuid',
        'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'price' => '',
        'discount' => 'nullable|integer',
        'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
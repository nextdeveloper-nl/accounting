<?php

namespace NextDeveloper\Accounting\Http\Requests\ContractItemsPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContractItemsPerspectiveUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'object_type' => 'nullable|string',
'object_id' => 'nullable',
'accounting_contract_id' => 'nullable|exists:accounting_contracts,uuid|uuid',
'term_starts' => 'nullable|date',
'term_ends' => 'nullable|date',
'price' => 'nullable',
'discount' => 'nullable|integer',
'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
'contract_type' => 'nullable|string',
'is_signed' => 'nullable|boolean',
'is_approved' => 'nullable|boolean',
'account_name' => 'nullable|string',
'iam_account_type_id' => 'nullable|exists:iam_account_types,uuid|uuid',
'accounting_identifier' => 'nullable|string',
'credit' => 'nullable',
'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
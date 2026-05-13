<?php

namespace NextDeveloper\Accounting\Http\Requests\ContractsPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContractsPerspectiveUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'name' => 'nullable|string',
'description' => 'nullable|string',
'term_starts' => 'nullable|date',
'term_ends' => 'nullable|date',
'is_signed' => 'nullable|boolean',
'is_approved' => 'nullable|boolean',
'contract_item_count' => 'nullable|integer',
'account_name' => 'nullable|string',
'iam_account_type_id' => 'nullable|exists:iam_account_types,uuid|uuid',
'accounting_identifier' => 'nullable|string',
'credit' => 'nullable',
'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
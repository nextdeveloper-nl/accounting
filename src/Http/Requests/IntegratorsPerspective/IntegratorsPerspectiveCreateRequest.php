<?php

namespace NextDeveloper\Accounting\Http\Requests\IntegratorsPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class IntegratorsPerspectiveCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'name' => 'nullable|string',
'phone_number' => 'nullable|string',
'common_country_id' => 'nullable|exists:common_countries,uuid|uuid',
'common_domain_id' => 'nullable|exists:common_domains,uuid|uuid',
'iam_account_type_id' => 'nullable|exists:iam_account_types,uuid|uuid',
'tax_number' => 'nullable|string',
'tax_office' => 'nullable|string',
'accounting_identifier' => 'nullable|string',
'credit' => 'nullable',
'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
'common_currency_code' => 'nullable|string',
'tr_mersis' => 'nullable|string',
'trade_office' => 'nullable|string',
'trade_office_number' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
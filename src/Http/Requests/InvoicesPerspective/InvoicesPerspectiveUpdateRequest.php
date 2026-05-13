<?php

namespace NextDeveloper\Accounting\Http\Requests\InvoicesPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoicesPerspectiveUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'term_year' => 'nullable|integer',
'term_month' => 'nullable|integer',
'amount' => 'nullable',
'is_paid' => 'nullable|boolean',
'is_payable' => 'nullable|boolean',
'is_refund' => 'nullable|boolean',
'is_sealed' => 'nullable|boolean',
'is_commission_invoice' => 'nullable|boolean',
'note' => 'nullable|string',
'name' => 'nullable|string',
'common_country_id' => 'nullable|exists:common_countries,uuid|uuid',
'common_domain_id' => 'nullable|exists:common_domains,uuid|uuid',
'iam_account_type_id' => 'nullable|exists:iam_account_types,uuid|uuid',
'accounting_identifier' => 'nullable|string',
'credit' => 'nullable',
'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
'common_currency_code' => 'nullable|string',
'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
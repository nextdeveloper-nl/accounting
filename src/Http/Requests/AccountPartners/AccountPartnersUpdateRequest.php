<?php

namespace NextDeveloper\Accounting\Http\Requests\AccountPartners;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AccountPartnersUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
            'partnership_role_id' => 'nullable|exists:accounting_partnership_roles,uuid|uuid',
            'partnership_id' => 'nullable|exists:accounting_partnerships,uuid|uuid',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

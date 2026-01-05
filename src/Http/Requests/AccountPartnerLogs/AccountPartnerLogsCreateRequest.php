<?php

namespace NextDeveloper\Accounting\Http\Requests\AccountPartnerLogs;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class AccountPartnerLogsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_account_id' => 'required|exists:accounting_accounts,uuid|uuid',
        'partner_type' => 'required',
        'old_partner_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'new_partner_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'started_at' => 'date',
        'finished_at' => 'nullable|date',
        'reason' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
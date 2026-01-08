<?php

namespace NextDeveloper\Accounting\Http\Requests\PartnerAssignments;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnerAssignmentsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'type' => 'nullable',
        'old_partner_id' => 'nullable|exists:old_partners,uuid|uuid',
        'new_partner_id' => 'nullable|exists:new_partners,uuid|uuid',
        'started_at' => 'date',
        'finished_at' => 'nullable|date',
        'reason' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
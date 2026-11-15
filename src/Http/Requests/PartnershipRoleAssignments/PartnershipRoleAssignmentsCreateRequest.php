<?php

namespace NextDeveloper\Accounting\Http\Requests\PartnershipRoleAssignments;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnershipRoleAssignmentsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'partnership_id' => 'required|exists:accounting_partnerships,uuid|uuid',
            'partnership_role_id' => 'required|exists:accounting_partnership_roles,uuid|uuid',
            'status' => 'nullable|string',
            'started_at' => 'nullable|date',
            'finished_at' => 'nullable|date',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

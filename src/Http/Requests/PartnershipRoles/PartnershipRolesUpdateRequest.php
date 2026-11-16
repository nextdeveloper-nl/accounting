<?php

namespace NextDeveloper\Accounting\Http\Requests\PartnershipRoles;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnershipRolesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'slug' => 'nullable|string',
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'commission_rate' => 'nullable|numeric|between:0,1',
            'parent_override_rate' => 'nullable|numeric|between:0,1',
            'commission_basis' => 'nullable|string',
            'priority' => 'nullable|integer',
            'allows_multiple_per_customer' => 'nullable|boolean',
            'is_default_for_signup_code' => 'nullable|boolean',
            'allowed_child_role_ids' => 'nullable',
            'permissions' => 'nullable',
            'status' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

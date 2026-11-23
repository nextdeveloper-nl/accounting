<?php

namespace NextDeveloper\Accounting\Http\Requests\Partnerships;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

/**
 * Request to apply to the partner program of another company.
 *
 * The applicant is NOT accepted from the client: it is always the account the user acts as. The program is given by
 * the public id of the IAM account that runs it, all ownership checks of the roles are done by
 * PartnershipsService::applyToProgram().
 */
class PartnershipApplyRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules() {
        return [
            'tenant_account_id' => 'required|uuid|exists:iam_accounts,uuid',
            'role_ids'          => 'required|array|min:1|max:20',
            'role_ids.*'        => 'required|uuid|distinct|exists:accounting_partnership_roles,uuid',
        ];
    }
}

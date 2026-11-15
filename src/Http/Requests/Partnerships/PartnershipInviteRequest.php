<?php

namespace NextDeveloper\Accounting\Http\Requests\Partnerships;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

/**
 * Request to invite an existing company (IAM account) to the partner program of the account the user acts as.
 *
 * The tenant is NOT accepted from the client: it is always the current account. All ownership checks of the given
 * ids (roles, parent partnership) are done by PartnershipsService::invitePartner().
 */
class PartnershipInviteRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules() {
        return [
            'partner_account_id'    => 'required|uuid|exists:iam_accounts,uuid',
            'role_ids'              => 'required|array|min:1|max:20',
            'role_ids.*'            => 'required|uuid|distinct|exists:accounting_partnership_roles,uuid',
            'parent_partnership_id' => 'nullable|uuid|exists:accounting_partnerships,uuid',
        ];
    }
}

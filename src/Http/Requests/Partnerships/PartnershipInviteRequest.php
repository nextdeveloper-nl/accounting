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
            //  The company is given either by its account id (found with the candidate search) or by the email of its
            //  account owner. One of them is required. When both are sent the account id wins.
            'partner_account_id'    => 'required_without:partner_email|nullable|uuid|exists:iam_accounts,uuid',
            'partner_email'         => 'required_without:partner_account_id|nullable|email|max:254',
            'role_ids'              => 'required|array|min:1|max:20',
            'role_ids.*'            => 'required|uuid|distinct|exists:accounting_partnership_roles,uuid',
            'parent_partnership_id' => 'nullable|uuid|exists:accounting_partnerships,uuid',
        ];
    }
}

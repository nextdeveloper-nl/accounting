<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Services\AbstractServices\AbstractPartnershipRoleAssignmentsService;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for PartnershipRoleAssignments
 *
 * Class PartnershipRoleAssignmentsService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class PartnershipRoleAssignmentsService extends AbstractPartnershipRoleAssignmentsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Multi-tenant partnership: the tenant (program owner) is NEVER taken from the request. It is always the
     * account the current user is acting as, so a client cannot create rows inside another tenant.
     *
     * @param  array $data
     * @return mixed
     */
    public static function create(array $data)
    {
        unset($data['tenant_account_id']);
        $data['tenant_account_id'] = UserHelper::currentAccount()->id;

        return parent::create($data);
    }

    /**
     * Multi-tenant partnership: the tenant of an existing row can never be changed through the API.
     *
     * @param  $id
     * @param  array $data
     * @return mixed
     */
    public static function update($id, array $data)
    {
        unset($data['tenant_account_id']);

        return parent::update($id, $data);
    }
}

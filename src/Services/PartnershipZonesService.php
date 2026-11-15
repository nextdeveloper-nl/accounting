<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Services\AbstractServices\AbstractPartnershipZonesService;
use NextDeveloper\Accounting\Helpers\PartnershipTenantGuard;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for PartnershipZones
 *
 * Class PartnershipZonesService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class PartnershipZonesService extends AbstractPartnershipZonesService
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
        //  Every referenced partnership / role / program / customer must belong to OUR tenant (uuids are checked
        //  before they are converted to ids, independent of what the role scope shows).
        PartnershipTenantGuard::assertReferences($data);

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

        //  Same ownership check on update, otherwise a row could be re-pointed to another tenant's objects.
        PartnershipTenantGuard::assertReferences($data);

        return parent::update($id, $data);
    }
}

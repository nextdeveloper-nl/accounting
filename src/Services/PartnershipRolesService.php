<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Services\AbstractServices\AbstractPartnershipRolesService;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for PartnershipRoles
 *
 * Class PartnershipRolesService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class PartnershipRolesService extends AbstractPartnershipRolesService
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

        //  The UI does not send a slug: derive one from the name, unique inside the tenant (adds -2, -3 ... on clash).
        if (empty($data['slug'])) {
            $data['slug'] = self::generateUniqueSlug((string)($data['name'] ?? ''), $data['tenant_account_id']);
        }

        return parent::create($data);
    }

    /**
     * Builds a slug from the role name that does not clash with another role of the same tenant.
     *
     * @param  string $name
     * @param  int    $tenantAccountId
     * @return string
     */
    private static function generateUniqueSlug(string $name, int $tenantAccountId): string
    {
        $base = Str::slug($name) ?: 'role';
        $slug = $base;
        $i = 2;

        //  Explicit tenant condition and no AuthorizationScope: we must see every role of the tenant to avoid a clash.
        while (PartnershipRoles::withoutGlobalScopes()
            ->where('tenant_account_id', $tenantAccountId)
            ->where('slug', $slug)
            ->exists()) {
            $slug = $base . '-' . $i++;
        }

        return $slug;
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

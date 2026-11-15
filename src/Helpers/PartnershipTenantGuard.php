<?php

namespace NextDeveloper\Accounting\Helpers;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\PartnerPrograms;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Multi-tenant partnership: makes sure that every object a request points to (by uuid) belongs to the tenant the
 * current user is acting as.
 *
 * Why this exists: DatabaseHelper::uuidToId() resolves uuids through AuthorizationScope, and the scope of a role can
 * legitimately show rows of another tenant (for example a tenant manager also sees their OWN partner row in other
 * tenants). Relying on the scope alone would let a tenant link its rows to another tenant's partnership or role.
 * The tenant is therefore checked explicitly, independent of any role scope.
 */
class PartnershipTenantGuard
{
    /**
     * Throws if the object referenced by the given uuid does not belong to the current tenant.
     *
     * @param string      $modelClass Model with a tenant_account_id column
     * @param string|null $uuid       The (public) id coming from the request. Null / missing is ignored
     *                                (required-ness is the job of the request rules).
     * @param string      $field      Name of the request field, used in the error message
     *
     * @throws NotAllowedException
     */
    public static function assertOwned(string $modelClass, ?string $uuid, string $field): void
    {
        if (!$uuid) {
            return;
        }

        $tenantId = (int)UserHelper::currentAccount()->id;

        //  Deliberately bypassing AuthorizationScope: we want the real tenant of the row, not what a role shows us.
        $object = $modelClass::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $uuid)
            ->first();

        if (!$object || (int)$object->tenant_account_id !== $tenantId) {
            //  Same message for "does not exist" and "belongs to someone else", so uuids cannot be probed.
            throw new NotAllowedException(
                'The object given in "' . $field . '" does not exist or does not belong to your partner program.'
            );
        }
    }

    /**
     * Checks all tenant owned references of a request payload.
     *
     * @param array $data Request data (uuids), before they are converted to ids
     */
    public static function assertReferences(array $data): void
    {
        $map = [
            'partnership_id'         => Partnerships::class,
            'default_partnership_id' => Partnerships::class,
            'partnership_role_id'    => PartnershipRoles::class,
            'partner_program_id'     => PartnerPrograms::class,
            //  The customer account of an account partner link must be a customer of this tenant.
            'accounting_account_id'  => Accounts::class,
        ];

        foreach ($map as $field => $class) {
            if (array_key_exists($field, $data)) {
                self::assertOwned($class, $data[$field], $field);
            }
        }
    }
}

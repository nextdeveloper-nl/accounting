<?php

namespace NextDeveloper\Accounting\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Tenant side role of the multi-tenant partnership program.
 *
 * A user with this role manages the partner program OF THE ACCOUNT THEY ARE ACTING AS (the tenant): the program
 * settings, partnership roles, the partners, their roles, zones and customer links.
 *
 * IMPORTANT: AuthorizationScope applies only the FIRST role (ordered by level) whose canBeApplied() is true, roles
 * are never merged. That is why canBeApplied() is limited to the partnership tables and NOT to the whole accounting
 * prefix: otherwise this role would also take over invoices, credit cards, etc. for its users.
 */
class AccountingPartnershipManagerRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'partnership-manager';

    //  Must be lower than accounting-user (150) so it is evaluated first for the partnership tables.
    public const LEVEL = 60;

    public const DESCRIPTION = 'Partnership manager manages the partner program of their own account: partnership' .
    ' roles, partners, partner zones and customer assignments.';

    public const DB_PREFIX = 'accounting';

    /**
     * Tables fully owned by the tenant (all of them have tenant_account_id).
     */
    public const TENANT_TABLES = [
        'accounting_partner_programs',
        'accounting_partnership_roles',
        'accounting_partnership_role_assignments',
        'accounting_account_partners',
        'accounting_partner_program_zones',
        'accounting_partnership_zones',
        'accounting_partnerships',
    ];

    /**
     * Restricts the query to the tenant of the current account.
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        if (!in_array($table, self::TENANT_TABLES)) {
            //  Not one of our tables, canBeApplied() should have prevented this. Fail closed.
            $builder->whereRaw('1 = 0');
            return;
        }

        $currentAccountId = UserHelper::currentAccount()->id;

        if ($table === 'accounting_partnerships') {
            //  A tenant manager sees their partners, and also their OWN partnership rows in other tenants
            //  (the account may be a partner somewhere else), so that this role does not hide them.
            $builder->where(function (Builder $q) use ($currentAccountId) {
                $q->where('tenant_account_id', $currentAccountId);

                try {
                    $q->orWhere('accounting_account_id', AccountingHelper::getAccount()->id);
                } catch (\Throwable $e) {
                    //  No accounting account provisioned yet, only the tenant filter applies.
                }
            });

            return;
        }

        $builder->where('tenant_account_id', $currentAccountId);
    }

    public function checkPrivileges(?Users $users = null)
    {
        //return UserHelper::hasRole(self::NAME, $users);
    }

    public function getModule()
    {
        return 'accounting';
    }

    public function allowedOperations(): array
    {
        return [
            'accounting_partner_programs:read',
            'accounting_partner_programs:create',
            'accounting_partner_programs:update',

            'accounting_partnership_roles:read',
            'accounting_partnership_roles:create',
            'accounting_partnership_roles:update',
            'accounting_partnership_roles:delete',

            'accounting_partnership_role_assignments:read',
            'accounting_partnership_role_assignments:create',
            'accounting_partnership_role_assignments:update',
            'accounting_partnership_role_assignments:delete',

            'accounting_account_partners:read',
            'accounting_account_partners:create',
            'accounting_account_partners:update',
            'accounting_account_partners:delete',

            'accounting_partner_program_zones:read',
            'accounting_partner_program_zones:create',
            'accounting_partner_program_zones:update',
            'accounting_partner_program_zones:delete',

            'accounting_partnership_zones:read',
            'accounting_partnership_zones:create',
            'accounting_partnership_zones:update',
            'accounting_partnership_zones:delete',

            //  Partners are revoked (status change), not deleted, so there is no delete here.
            'accounting_partnerships:read',
            'accounting_partnerships:create',
            'accounting_partnerships:update',
        ];
    }

    /**
     * Update is allowed only on rows of OUR tenant. The default policy compares iam_account_id, which is the creator
     * account, and that is not the same thing as the tenant (e.g. rows created by platform staff).
     */
    public function checkUpdatePolicy(Model $model, Users $user): bool
    {
        if (!in_array($model->getTable() . ':update', $this->allowedOperations())) {
            return false;
        }

        return $this->isOwnTenant($model);
    }

    public function checkDeletePolicy(Model $model, Users $user): bool
    {
        if (!in_array($model->getTable() . ':delete', $this->allowedOperations())) {
            return false;
        }

        return $this->isOwnTenant($model);
    }

    private function isOwnTenant(Model $model): bool
    {
        return $model->tenant_account_id !== null
            && (int)$model->tenant_account_id === (int)UserHelper::currentAccount()->id;
    }

    public function getLevel(): int
    {
        return self::LEVEL;
    }

    public function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * Only the partnership tables, see the class comment.
     */
    public function canBeApplied($column)
    {
        return in_array($column, self::TENANT_TABLES, true);
    }

    public function getDbPrefix()
    {
        return self::DB_PREFIX;
    }

    public function checkRules(?Users $users = null): bool
    {
        return false;
    }
}

<?php

namespace NextDeveloper\Accounting\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Partner side role of the multi-tenant partnership program.
 *
 * This role takes over the name `partnership-user` of the legacy Partnership module (that role is a default role
 * of every account). It therefore keeps the legacy behaviour for the legacy `partnership_*` tables and adds
 * read only access to the partner's OWN partnership data in the accounting tables.
 *
 * IMPORTANT: AuthorizationScope applies only the FIRST role (ordered by level) whose canBeApplied() is true, roles
 * are never merged. canBeApplied() is therefore limited to the tables this role actually handles.
 */
class AccountingPartnershipUserRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'partnership-user';

    //  Same level as the legacy role, lower than accounting-user (150).
    public const LEVEL = 120;

    public const DESCRIPTION = 'Partnership user can manage his services, marketing and production items, give support' .
    ' to their customers and see their own partnership: roles, customers and zones.';

    public const DB_PREFIX = 'accounting';

    /**
     * Partner side tables, all read only and scoped to the partner's own active partnerships.
     */
    public const PARTNER_TABLES = [
        'accounting_partner_programs',
        'accounting_partnership_roles',
        'accounting_partnership_role_assignments',
        'accounting_account_partners',
        'accounting_partnership_zones',
    ];

    /**
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        //  Legacy Partnership module behaviour, unchanged.
        if (Str::startsWith($table, 'partnership_')) {
            if ($table == 'partnership_accounts_perspective' || $table == 'partnership_accounts') {
                //  The legacy filter was `where(iam_account_id = mine) orWhere(is_distributor)` WITHOUT grouping, so the OR
                //  escaped every other condition (soft delete filtering, other scopes) and exposed more rows than meant.
                //  Grouped here: same intended rows (mine + distributors), but still inside all other conditions.
                $currentAccountId = UserHelper::currentAccount()->id;

                $builder->where(function (Builder $q) use ($currentAccountId) {
                    $q->where('iam_account_id', $currentAccountId)
                        ->orWhere('is_distributor', true);
                });

                return;
            }

            $builder->where('iam_account_id', UserHelper::currentAccount()->id);

            return;
        }

        if (!in_array($table, self::PARTNER_TABLES)) {
            //  Not one of our tables, canBeApplied() should have prevented this. Fail closed.
            $builder->whereRaw('1 = 0');

            return;
        }

        $myPartnershipIds = $this->myActivePartnershipIds();

        if (empty($myPartnershipIds)) {
            //  Not a partner anywhere: nothing to show.
            $builder->whereRaw('1 = 0');

            return;
        }

        switch ($table) {
            case 'accounting_partnership_role_assignments':
            case 'accounting_account_partners':
            case 'accounting_partnership_zones':
                $builder->whereIn('partnership_id', $myPartnershipIds);
                break;

            case 'accounting_partnership_roles':
                //  Only the roles I actually hold, so rates of other roles of the tenant are not exposed.
                //  Plain DB query on purpose: going through the models would re-enter AuthorizationScope.
                $builder->whereIn(
                    'id',
                    DB::table('accounting_partnership_role_assignments')
                        ->whereIn('partnership_id', $myPartnershipIds)
                        ->whereNull('finished_at')
                        ->whereNull('deleted_at')
                        ->select('partnership_role_id')
                );
                break;

            case 'accounting_partner_programs':
                //  The programs (branding, name) of the tenants I am a partner of.
                $builder->whereIn(
                    'tenant_account_id',
                    DB::table('accounting_partnerships')
                        ->whereIn('id', $myPartnershipIds)
                        ->select('tenant_account_id')
                );
                break;
        }
    }

    /**
     * Ids of the ACTIVE partnerships of the current account (pending/rejected/revoked ones give no access).
     */
    private function myActivePartnershipIds(): array
    {
        try {
            $accountingAccountId = AccountingHelper::getAccount()->id;
        } catch (\Throwable $e) {
            return [];
        }

        return DB::table('accounting_partnerships')
            ->where('accounting_account_id', $accountingAccountId)
            ->where('status', 'active')
            ->whereNull('deleted_at')
            ->pluck('id')
            ->all();
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
            //  Legacy Partnership module operations (kept as they were)
            'partnership_accounts:create',
            'partnership_accounts:read',
            'partnership_accounts:update',
            'partnership_distribution:read',
            'partnership_distribution:create',
            'partnership_distribution:update',
            'partnership_distribution:delete',
            'partnership_distribution:cancel',
            'partnership_marketing:read',
            'partnership_marketing:create',
            'partnership_marketing:update',
            'partnership_marketing:delete',
            'partnership_marketing:cancel',
            'partnership_production:read',
            'partnership_production:create',
            'partnership_production:update',
            'partnership_production:delete',
            'partnership_production:cancel',

            //  Multi-tenant partnership: partner side is strictly read only
            'accounting_partner_programs:read',
            'accounting_partnership_roles:read',
            'accounting_partnership_role_assignments:read',
            'accounting_account_partners:read',
            'accounting_partnership_zones:read',
        ];
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
     * The partner tables above plus the legacy partnership_* tables, see the class comment.
     */
    public function canBeApplied($column)
    {
        return in_array($column, self::PARTNER_TABLES, true) || Str::startsWith($column, 'partnership_');
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

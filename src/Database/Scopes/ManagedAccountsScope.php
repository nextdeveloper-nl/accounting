<?php

namespace NextDeveloper\Accounting\Database\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Restricts accounting perspectives to the accounts the acting staff member manages.
 *
 * Applies only to the managed-only staff role (plusclouds-sales-manager). Platform-wide roles keep their
 * normal visibility, even if the user also holds the managed-only role.
 * Managed = crm_account_managers row for the acting user AND acting account, pointing to a crm_account
 * whose iam_account_id owns the accounting account.
 */
class ManagedAccountsScope implements Scope
{
    private const MANAGED_ONLY_ROLE = 'plusclouds-sales-manager';

    private const PLATFORM_ROLES = ['system-admin', 'iam-admin', 'sales-admin', 'sales-manager-admin'];

    public function apply(Builder $builder, Model $model): void
    {
        // Not logged in / console: other scopes deal with it.
        if (! UserHelper::me() || ! UserHelper::hasRole(self::MANAGED_ONLY_ROLE)) {
            return;
        }

        foreach (self::PLATFORM_ROLES as $role) {
            if (UserHelper::hasRole($role)) {
                return;
            }
        }

        $userId = (int) UserHelper::me()->id;
        $accountId = (int) UserHelper::currentAccount()->id;

        $builder->whereRaw('accounting_account_id in (
            select aa.id from accounting_accounts aa
            where aa.iam_account_id in (
                select ca.iam_account_id from crm_account_managers cam
                join crm_accounts ca on ca.id = cam.crm_account_id
                where cam.iam_user_id = ' . $userId . '
                  and cam.iam_account_id = ' . $accountId . '
                  and cam.deleted_at is null
            )
        )');
    }
}

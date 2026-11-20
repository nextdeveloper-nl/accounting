<?php

namespace NextDeveloper\Accounting\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingManagerRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'accounting-manager';

    //  Customer manager level (leo4#1304), was 20: below accounting-admin (10) and accounting-user (150), outside the
    //  admin level range (<= 100) that customers must not reach.
    public const LEVEL = 120;

    public const DESCRIPTION = 'Accounting operations manager who has access to all objects in this module.';

    public const DB_PREFIX = 'accounting';

    /**
     * Applies basic member role sql for Eloquent
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        //  Multi-tenant partnership: accounting_partnerships now holds the partners of EVERY tenant, so this role must
        //  not see them all. It sees its own tenant's partners, plus its own partnership rows in other tenants.
        //  Cross tenant visibility is for the platform accounting-admin role only.
        if ($model->getTable() == 'accounting_partnerships') {
            $currentAccountId = UserHelper::currentAccount()->id;

            $builder->where(function (Builder $q) use ($currentAccountId) {
                $q->where('tenant_account_id', $currentAccountId);

                try {
                    $q->orWhere('accounting_account_id', \NextDeveloper\Accounting\Helpers\AccountingHelper::getAccount()->id);
                } catch (\Throwable $e) {
                    //  No accounting account provisioned yet, only the tenant filter applies.
                }
            });

            return;
        }

        //  Multi-tenant partnership: the accounting manager sees only the commissions of the tenant (account) they act as.
        //  Cross tenant visibility is for the platform accounting-admin role only.
        if ($model->getTable() == 'accounting_invoice_commissions') {
            $builder->where('tenant_account_id', UserHelper::currentAccount()->id);

            return;
        }

        $partners = [
            'accounting_integrators_perspective',
            'accounting_distributors_perspective',
            'accounting_sales_partners_perspective',
            'accounting_affiliates_perspective',
            'accounting_vendors_perspective',

            'accounting_accounts_perspective',
            'accounting_affiliates_perspective',
            'accounting_contract_items_perspective',
            'accounting_contracts_perspective',
            'accounting_distributors_perspective',
            'accounting_integrators_perspective',
            'accounting_invoice_items_perspective',
            'accounting_invoices_perspective',
            'accounting_partnerships_perspective',
            'accounting_sales_partners_perspective',
            'accounting_vendors_perspective'
        ];

        if (in_array($model->getTable(), $partners)) {
            return;
        }

        if (
            $model->getTable() == 'accounting_credit_cards'
        ) {
            $builder->where('iam_account_id', UserHelper::currentAccount()->id);
            return;
        }

        //  Every other accounting table (payment gateways with live Stripe / iyzico data, ...) used to fall through
        //  without any filter, so the role saw the rows of every account. Default to the acting account's own rows.
        if (\NextDeveloper\Commons\Helpers\DatabaseHelper::isColumnExists($model->getTable(), 'iam_account_id')) {
            $builder->where($model->getTable() . '.iam_account_id', UserHelper::currentAccount()->id);
        }
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
            'accounting_integrators_perspective:read',
            'accounting_distributors_perspective:read',
            'accounting_sales_partners_perspective:read',
            'accounting_affiliates_perspective:read',
            'accounting_vendors_perspective:read',

            'accounting_accounts_perspective:read',
            'accounting_affiliates_perspective:read',
            'accounting_contract_items_perspective:read',
            'accounting_contracts_perspective:read',
            'accounting_distributors_perspective:read',
            'accounting_integrators_perspective:read',
            'accounting_invoice_items_perspective:read',
            'accounting_invoices_perspective:read',
            'accounting_partnerships_perspective:read',
            'accounting_sales_partners_perspective:read',
            'accounting_vendors_perspective:read',

            'accounting_accounts:read',
            'accounting_accounts:create',
            'accounting_accounts:update',
            'accounting_accounts:delete',

            'accounting_credit_cards:read',
            'accounting_credit_cards:create',
            'accounting_credit_cards:update',
            'accounting_credit_cards:delete',

            'accounting_invoices:read',
            'accounting_invoices:create',
            'accounting_invoices:update',
            'accounting_invoices:delete',

            'accounting_invoice_items:read',
            'accounting_invoice_items:create',
            'accounting_invoice_items:update',
            'accounting_invoice_items:delete',

            'accounting_contracts:read',
            'accounting_contracts:create',
            'accounting_contracts:update',
            'accounting_contracts:delete',

            'accounting_contract_items:read',
            'accounting_contract_items:create',
            'accounting_contract_items:update',
            'accounting_contract_items:delete',

            'accounting_payment_gateways:read',
            'accounting_payment_gateways:create',
            'accounting_payment_gateways:update',
            'accounting_payment_gateways:delete',

            'accounting_promo_codes:read',
            'accounting_promo_codes:create',
            'accounting_promo_codes:update',
            'accounting_promo_codes:delete',

            'accounting_transactions:read',
            'accounting_transactions:create',
            'accounting_transactions:update',
            'accounting_transactions:delete',

            'accounting_payment_checkout_sessions:read',
            'accounting_payment_checkout_sessions:create',
            'accounting_payment_checkout_sessions:update',
            'accounting_payment_checkout_sessions:delete',

            'accounting_partnerships:read',
            'accounting_partnerships:create',
            'accounting_partnerships:update',
            'accounting_partnerships:delete',

            // Multi-tenant partnership: commission records of OUR tenant (see apply()). The other partnership
            // tables are handled by partnership-manager / partnership-user.
            'accounting_invoice_commissions:read',
        ];
    }

    //  This means that the accounting manager can update any model in the accounting module.
    //  This is a very powerful role, so be careful with it.
    public function checkUpdatePolicy(Model $model, Users $user): bool
    {
        if ($model->getTable() == 'accounting_accounts')
            return true;

        return parent::checkUpdatePolicy($model, $user);
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

    public function canBeApplied($column)
    {
        //  Multi-tenant partnership tables belong to partnership-manager / partnership-user. If this role claimed them
        //  it would apply no filter and expose every tenant's data (the first matching role wins, roles never merge).
        if (in_array($column, [
            'accounting_partner_programs',
            'accounting_partnership_roles',
            'accounting_partnership_role_assignments',
            'accounting_account_partners',
            'accounting_partner_program_zones',
            'accounting_partnership_zones',
        ], true)) {
            return false;
        }

        if (self::DB_PREFIX === '*') {
            return true;
        }

        if (Str::startsWith($column, self::DB_PREFIX)) {
            return true;
        }

        return false;
    }

    public function getDbPrefix()
    {
        return self::DB_PREFIX;
    }

    public function checkRules(Users $users): bool
    {
        return true;
    }
}

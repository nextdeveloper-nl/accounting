<?php

namespace NextDeveloper\Accounting\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\CRM\Database\Models\AccountManagers;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingAdminRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'accounting-admin';

    public const LEVEL = 10;

    public const DESCRIPTION = 'Accounting Admin';

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
        if(
            $model->getTable() == 'accounting_credit_cards'
        ) {
            $builder->where('iam_account_id', UserHelper::currentAccount()->id);
            return;
        }
    }

    public function checkPrivileges(Users $users = null)
    {
        //return UserHelper::hasRole(self::NAME, $users);
    }

    public function getModule()
    {
        return 'accounting';
    }

    public function allowedOperations() :array
    {
        return [
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

    public function canBeApplied($column)
    {
        if(self::DB_PREFIX === '*') {
            return true;
        }

        if(Str::startsWith($column, self::DB_PREFIX)) {
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

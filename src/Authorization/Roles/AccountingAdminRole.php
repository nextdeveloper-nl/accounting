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
        /**
         * Here user will be able to list all models, because by default, sales manager can see everybody.
         */
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
            'accounting_transactions:read',
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

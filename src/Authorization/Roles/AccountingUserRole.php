<?php

namespace NextDeveloper\Accounting\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\CRM\Database\Models\AccountManagers;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingUserRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'accounting-user';

    public const LEVEL = 150;

    public const DESCRIPTION = 'Accounting user whose can see their invoices, credit cards, transactions and' .
    ' other accounting related data.';

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
            $model->getTable() == 'accounting_invoices' ||
            $model->getTable() == 'accounting_transactions'
        ) {
            $myAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', UserHelper::currentAccount()->id)
                ->first();

            $builder->where('accounting_account_id', $myAccount->id);
        }

        if(
            $model->getTable() == 'accounting_accounts' ||
            $model->getTable() == 'accounting_credit_cards' ||

            // this payment gateway is owned by the account that is why we put here
            //  In security sense this would be logical to seperate
            $model->getTable() == 'accounting_payment_gateways'
        ) {
            $builder->where('iam_account_id', UserHelper::currentAccount()->id);
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
            'accounting_credit_cards:read',
            'accounting_credit_cards:create',
            'accounting_credit_cards:update',
            'accounting_credit_cards:delete',
            'accounting_invoices:read',
            'accounting_invoice_items:read',
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
        // TODO: Implement checkRules() method.
    }
}

<?php

namespace NextDeveloper\Accounting\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingPartnerRole extends AbstractRole implements IAuthorizationRole
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
        $partners = [
            'accounting_integrators_perspective',
            'accounting_distributors_perspective',
            'accounting_sales_partners_perspective',
            'accounting_affiliates_perspective',
            'accounting_vendors_perspective',
        ];

        if(in_array($model->getTable(), $partners)) {
            return;
        }

        if(
            $model->getTable() == 'accounting_invoices' ||
            $model->getTable() == 'accounting_invoices_perspective' ||
            $model->getTable() == 'accounting_transactions' ||
            $model->getTable() == 'accounting_contracts' ||
            $model->getTable() == 'accounting_contracts_perspective' ||
            $model->getTable() == 'accounting_partnerships'
        ) {
            $myAccount = AccountingHelper::getAccount();

            $builder->where('accounting_account_id', $myAccount->id);

            return;
        }

        if(
            $model->getTable() == 'accounting_affiliates_perspective' ||
            $model->getTable() == 'accounting_distributors_perspective' ||
            $model->getTable() == 'accounting_sales_partners_perspective' ||
            $model->getTable() == 'accounting_vendors_perspective' ||
            $model->getTable() == 'accounting_integrators_perspective'
        ) {
            return;
        }

        if(
            $model->getTable() == 'accounting_accounts' ||
            $model->getTable() == 'accounting_credit_cards' ||

            // this payment gateway is owned by the account that is why we put here
            //  In security sense this would be logical to seperate
            $model->getTable() == 'accounting_payment_gateways'
        ) {
            $builder->where('iam_account_id', UserHelper::currentAccount()->id);
            return;
        }

        $builder->where('iam_account_id', UserHelper::currentAccount()->id);
    }

    public function getModule()
    {
        return 'accounting';
    }

    public function allowedOperations() :array
    {
        return [
            'accounting_integrators_perspective:read',
            'accounting_distributors_perspective:read',
            'accounting_sales_partners_perspective:read',
            'accounting_affiliates_perspective:read',
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

    public function checkRules(Users $users = null): bool
    {
        return false;
    }
}

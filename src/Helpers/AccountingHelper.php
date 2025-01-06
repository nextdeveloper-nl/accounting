<?php

namespace Helpers;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingHelper
{
    public static function getIamAccountFromContract(Contracts $contract) : ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $contract->accounting_account_id)
            ->first();

        return self::getIamAccount($accountingAccount);
    }

    /**
     * Returns the IamAccount by looking at Accounting account
     *
     * @param Accounts $account
     * @return \NextDeveloper\IAM\Database\Models\Accounts
     */
    public static function getIamAccount(Accounts $account) : ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        return \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->iam_account_id)
            ->first();
    }

    /**
     * Returns the Accounting account of the Iam Account
     *
     * @param \NextDeveloper\IAM\Database\Models\Accounts|null $account
     * @return Accounts
     */
    public static function getAccount(\NextDeveloper\IAM\Database\Models\Accounts $account = null) : Accounts
    {
        if(!$account)
            $account = UserHelper::currentAccount();

        return Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->id)
            ->first();
    }

    public static function getAccountFromCrmAccount(\NextDeveloper\CRM\Database\Models\Accounts $account): Accounts
    {
        return Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->iam_account_id)
            ->first();
    }
}

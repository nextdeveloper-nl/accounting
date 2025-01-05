<?php

namespace Helpers;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingHelper
{
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

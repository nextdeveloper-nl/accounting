<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * This class is responsible from managing the data for Accounts
 *
 * Class AccountsService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class AccountsService extends AbstractAccountsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public static function suspend(Accounts $account)
    {
        $account->update([
            'is_suspended' =>  'true'
        ]);

        return $account->fresh();
    }

    public static function suspendWithIamAccount(\NextDeveloper\IAM\Database\Models\Accounts $account) {
        $account = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->id)
            ->first();

        return self::suspend($account);
    }

    public static function unsuspend(Accounts $account)
    {
        $account->update([
            'is_suspended' =>  'false'
        ]);

        return $account->fresh();
    }

    public static function unsuspendWithIamAccount(\NextDeveloper\IAM\Database\Models\Accounts $account)
    {
        $account = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->id)
            ->first();

        return self::unsuspend($account);
    }
}

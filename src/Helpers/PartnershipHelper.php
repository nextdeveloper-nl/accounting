<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\IAM\Database\Models\Users;

class PartnershipHelper
{
    public static function getPartnership(Accounts|\NextDeveloper\IAM\Database\Models\Accounts $account)
    {
        switch (get_class($account)) {
            case Accounts::class:
                return Partnerships::where('iam_account_id', $account->iam_account_id)->first();
                break;
            case \NextDeveloper\IAM\Database\Models\Accounts::class:
                return Partnerships::where('iam_account_id', $account->id)->first();
                break;
            default:
                return null;
        }
    }

    public static function getPartnerAccountOwner(Accounts|\NextDeveloper\IAM\Database\Models\Accounts|string $account)
    {
        if(Str::isUuid($account)) {
            $account = Accounts::withoutGlobalScopes()
                ->where('uuid', $account)
                ->first();
        }

        if(is_int($account)) {
            $account = Accounts::withoutGlobalScopes()
                ->where('id', $account)
                ->first();
        }

        if(get_class($account) == 'NextDeveloper\IAM\Database\Models\Accounts') {
            return Users::withoutGlobalScopes()
                ->where('id', $account->iam_user_id)
                ->first();
        }

        $account = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScopes()
            ->where('id', $account->iam_account_id)
            ->first();

        return self::getPartnerAccountOwner($account);
    }
}

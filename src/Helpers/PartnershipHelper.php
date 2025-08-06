<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;

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
}

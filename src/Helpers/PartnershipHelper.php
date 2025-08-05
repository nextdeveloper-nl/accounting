<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;

class PartnershipHelper
{
    public static function getPartnership(Accounts $account)
    {
        return Partnerships::where('account_id', $account->iam_account_id)->first();
    }
}

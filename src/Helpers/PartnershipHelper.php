<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

class PartnershipHelper
{
    public static function getPartnerByCode($partnerCode) : ?Partnerships
    {
        if(!$partnerCode) return null;

        return Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('partner_code', $partnerCode)
            ->first();
    }

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

    public static function getPartnerAccountOwner(Accounts|string $account)
    {
        if(is_string($account)) {
            if(Str::isUuid($account)) {
                $account = Accounts::withoutGlobalScopes()
                    ->where('uuid', $account)
                    ->first();
            } else {
                $account = Accounts::withoutGlobalScopes()
                    ->where('id', $account)
                    ->first();
            }
        }

        $account = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScopes()
            ->where('id', $account->iam_account_id)
            ->first();

        return Users::withoutGlobalScopes()
            ->where('id', $account->iam_user_id)
            ->first();
    }
}

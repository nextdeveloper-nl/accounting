<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Helpers\PartnershipHelper;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractPartnershipsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Helpers\RoleHelper;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Models\Accounts;

/**
 * This class is responsible from managing the data for Partnerships
 *
 * Class PartnershipsService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class PartnershipsService extends AbstractPartnershipsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public static function create(array $data)
    {
        $partnership = null;

        if(array_key_exists('iam_account_id', $data)) {
            $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', $data['iam_account_id'])
                ->first();
        } else {
            $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', UserHelper::currentAccount()->id)
                ->first();
        }

        if($partnership) {
            return $partnership;
        }

        if (!isset($data['partner_code']) || empty($data['partner_code'])) {
            $codeNotValid = true;
            $randomString = '';

            while ($codeNotValid) {
                $randomString = Str::random(10);

                $exists = Partnerships::withoutGlobalScopes()
                    ->where('partner_code', $randomString)
                    ->first();

                if (!$exists)
                    $codeNotValid = false;
            }

            $data['partner_code'] = $randomString;
        }

        $data['customer_count'] = 0;
        $data['level'] = 1;

        $model = parent::create($data);

        return $model;
    }

    private static function addPartnerRoles(Partnerships $partnership)
    {
        $iamAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $partnership->iam_account_id)
            ->first();

        $user = Users::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $iamAccount->iam_user_id)
            ->first();

        if ($user) {
            RoleHelper::addUserToRole($user, 'accounting-partner');
        }
    }

    public static function approvePartnership(Accounts $account)
    {
        $partnership = PartnershipHelper::getPartnership($account);

        //  Here your partnership is approved email will be sent.

        $partnership->is_approved = true;
        $partnership->save();
    }
}

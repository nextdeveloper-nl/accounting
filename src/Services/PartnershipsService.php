<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Database\Models\Partnerships;
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
        $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', UserHelper::currentAccount()->id)
            ->first();

        if ($partnership) {
            self::addPartnerRoles($partnership);
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

        RoleHelper::addUserToRole(UserHelper::me(), 'accounting-user');

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
            RoleHelper::addUserToRole($user, 'partnership-user');
        }
    }
}

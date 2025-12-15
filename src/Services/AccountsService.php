<?php

namespace NextDeveloper\Accounting\Services;

use App\Envelopes\Affiliate\NewRegisterCameFromYourCode;
use Helpers\CrmHelper;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Helpers\PartnershipHelper;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountsService;
use NextDeveloper\Events\Services\Events;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\Commons\Exceptions\CannotCreateModelException;
use NextDeveloper\Communication\Helpers\Communicate;
use NextDeveloper\IAM\Helpers\UserHelper;

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

    public static function create($data)
    {
        throw new CannotCreateModelException('Accounting accounts cannot be created manually.');
    }

    public static function update($id, array $data)
    {
        if(!(UserHelper::hasRole('accounting-admin') || UserHelper::hasRole('accounting-manager'))) {
            unset($data['distributor_id']);
            unset($data['sales_partner_id']);
            unset($data['integrator_partner_id']);
            unset($data['affiliate_partner_id']);
        }

        if(array_key_exists('affiliate_partner_id', $data)) {
            if(!PartnershipHelper::getPartnerAccountOwner($data['affiliate_partner_id'])) {
                unset($data['affiliate_partner_id']);
            } else {
                $data['affiliate_partner_id'] = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $data['affiliate_partner_id'])->first()?->id;
            }
        }

        if(array_key_exists('integrator_partner_id', $data)) {
            if(!PartnershipHelper::getPartnerAccountOwner($data['integrator_partner_id'])) {
                unset($data['integrator_partner_id']);
            } else {
                $data['integrator_partner_id'] = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $data['integrator_partner_id'])->first()?->id;
            }
        }

        if(array_key_exists('sales_partner_id', $data)) {
            if(!PartnershipHelper::getPartnerAccountOwner($data['sales_partner_id'])) {
                unset($data['sales_partner_id']);
            } else {
                $data['sales_partner_id'] = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $data['sales_partner_id'])->first()?->id;
            }
        }

        if(array_key_exists('distributor_id', $data)) {
            if(!PartnershipHelper::getPartnerAccountOwner($data['distributor_id'])) {
                unset($data['distributor_id']);
            } else {
                $data['distributor_id'] = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $data['distributor_id'])->first()?->id;
            }
        }

        $updatedAccount = parent::update($id, $data);

        if(!$updatedAccount) {
            throw new \RuntimeException('Failed to update account.');
        }

        if(array_key_exists('distributor_id', $data)) {
            $partnerAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $data['distributor_id'])
                ->first();

            self::assignPartner($updatedAccount, $partnerAccount);
        }

        if(array_key_exists('sales_partner_id', $data)) {
            $partnerAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $data['sales_partner_id'])
                ->first();

            self::assignPartner($updatedAccount, $partnerAccount);
        }

        if(array_key_exists('integrator_partner_id', $data)) {
            $partnerAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $data['integrator_partner_id'])
                ->first();

            self::assignPartner($updatedAccount, $partnerAccount);
        }

        if(array_key_exists('affiliate_partner_id', $data)) {
            $partnerAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $data['affiliate_partner_id'])
                ->first();

            self::assignPartner($updatedAccount, $partnerAccount);
        }

        return $updatedAccount;
    }

    /**
     * @param \NextDeveloper\Accounting\Database\Models\Accounts $account
     * @param $partnerAccount
     * @param $as
     * @return void
     */
    public static function assignPartner($account, $partnerAccount, $as = 'integrator')
    {
        $crmAccount = \NextDeveloper\CRM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->iam_account_id)
            ->first();

        $iamAccountOfPartner = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $partnerAccount->iam_account_id)
            ->first();

        $ownerOfPartner = UserHelper::getAccountOwner($iamAccountOfPartner);

        CrmHelper::addAccountManager($crmAccount, $iamAccountOfPartner, $ownerOfPartner);

        Events::fire(
            'partner-assigned:NextDeveloper\Accounting\Accounts',
            $account
        );
    }

    public static function assignAffiliateToAccount(Accounts $customer, Partnerships $affiliateAccount)
    {
        $customer->affiliate_partner_id = $affiliateAccount->accounting_account_id;
        $customer->saveQuietly();

        // Here we need to send a notification to the affiliate
        $partnershipResponsible = UserHelper::getWithEmail(config('leo.notifications.partnership.responsible'));
        (new Communicate($partnershipResponsible))->sendEnvelope(new NewRegisterCameFromYourCode($customer));

        return $customer;
    }
}

<?php

namespace NextDeveloper\Accounting\Services;

use Google\Service\DisplayVideo\Partner;
use Helpers\CrmHelper;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Helpers\PartnershipHelper;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
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
    public static function update($id, array $data)
    {
        if(!UserHelper::hasRole('accounting-admin') || !UserHelper::hasRole('accounting-manager')) {
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

        $account = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $id)->first();

        $updatedAccount = parent::update($id, $data);

        if(!$updatedAccount) {
            throw new \RuntimeException('Failed to update account.');
        }

        if(array_key_exists('distributor_id', $data)) {
            self::assignPartner($updatedAccount, $data['distributor_id']);
        }

        if(array_key_exists('sales_partner_id', $data)) {
            self::assignPartner($updatedAccount, $data['sales_partner_id']);
        }

        if(array_key_exists('integrator_partner_id', $data)) {
            self::assignPartner($updatedAccount, $data['integrator_partner_id']);
        }

        if(array_key_exists('affiliate_partner_id', $data)) {
            self::assignPartner($updatedAccount, $data['affiliate_partner_id']);
        }

        return $updatedAccount;
    }

    private static function assignPartner($account, $partnerAccountingAccountId)
    {
        $crmAccountOfAccountingAccount = \NextDeveloper\CRM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->iam_account_id)
            ->first();

        $iamAccountOfDistributor = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $partnerAccountingAccountId)
            ->first();

        $ownerOfDistributor = PartnershipHelper::getPartnerAccountOwner($partnerAccountingAccountId);

        CrmHelper::addAccountManager($crmAccountOfAccountingAccount, $iamAccountOfDistributor, $ownerOfDistributor, $ownerOfDistributor);
    }
}

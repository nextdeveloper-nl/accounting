<?php

namespace NextDeveloper\Accounting\Helpers;

use App\Envelopes\CRM\Accounts\AssignedAsAccountManager;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Database\Models\Countries;
use NextDeveloper\Commons\Helpers\CountryHelper;
use NextDeveloper\Communication\Helpers\Communicate;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingHelper
{
    public static function setAccountAsSalesPartner(Accounts $customer, Accounts $provider)
    {
        $salesPartnerOwner = UserHelper::getAccountOwner(
            self::getIamAccount($provider)
        );

        //  We are also setting the sales partner for the distributor account
        $envelope = new AssignedAsAccountManager($salesPartnerOwner,
            AccountingHelper::getIamAccount($customer)
        );

        (new Communicate($salesPartnerOwner))->sendEnvelope($envelope);
    }

    public static function setMeAsSalesPartner(Accounts $account)
    {
        $myAccountingAccount = self::getAccountingAccount(
            UserHelper::currentAccount()->id
        );

        $account->updateQuietly([
            'sales_partner_id'  =>  $myAccountingAccount->id
        ]);

        return self::setAccountAsSalesPartner(
            customer: $account,
            provider: self::getAccountingAccount(UserHelper::currentAccount()->id)
        );
    }

    public static function getPaymentGatewayOfDistributor(Accounts $account)
    {
        return \NextDeveloper\Accounting\Database\Models\PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $account->id)
            ->where('is_active', true)
            ->first();
    }

    public static function getDistributorAccount(Accounts $account) {
        //  We are trying to find the accounting_account of the distributor
        $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->distributor_id)
            ->first();

        if(!$distributorAccount) {
            self::fixDistributorId($account);

            $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $account->distributor_id)
                ->first();
        }

        return $distributorAccount;
    }

    /**
     * Here we are trying to find the suitable distributor for the customer and then assign the customer
     * to the related distributor
     *
     * @param Accounts $account
     * @return Accounts
     */
    public static function fixDistributorId(Accounts $account) : Accounts
    {
        if($account->distributor_id){
            //  Not using this because it create infinite loop
            //$distributorAccount = self::getDistributorAccount($account);

            $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $account->distributor_id)
                ->first();

            if(!$distributorAccount) {
                Log::info(__METHOD__ . '| Distributor ID is not valid. Fixing it.');
            } else {
                Log::info(__METHOD__ . '| Distributor ID is valid. No need to fix it.');
                return $account;
            }
        }

        //  First we need to understand where the customer is actually from
        $iamAccount = self::getIamAccount($account);

        if(!$iamAccount) {
            Log::error(__METHOD__ . '| Cannot find the IAM account for the accounting account: ' . $account->id);
            return $account;
        }

        if($iamAccount->common_country_id) {
            $country = CountryHelper::getCountryById($iamAccount->common_country_id);
        } else {
            $country = null;
            Log::info(__METHOD__ . '| Customer (' . $iamAccount->name . ' | ' . $iamAccount->uuid . ') does not have a country id. Using the global provider.');
        }

        $provider = null;

        //  If the country is not set, we will use the global provider because we dont know where the customer is from.
        if(!$country) {
            $defaultProviderId = config('leo.providers.zones.global.distributor');

            $provider = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', $defaultProviderId)
                ->first();
        } else {
            //  If we know where the customer is then we should assign the related distributor
            //  For temporary we are assigning the default distributor
            $defaultProviderId = config('leo.providers.zones.' . strtolower($country->code) . '.distributor');

            if($defaultProviderId) {
                $provider = Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('iam_account_id', $defaultProviderId)
                    ->first();
            } else {
                $defaultProviderId = config('leo.providers.zones.global.distributor');
                $provider = Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('iam_account_id', $defaultProviderId)
                    ->first();
            }
        }

        $account->updateQuietly([
            'distributor_id'    =>  $provider->id
        ]);

        return $account->fresh();
    }

    public static function getIamAccountFromContract(Contracts $contract) : ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $contract->accounting_account_id)
            ->first();

        return self::getIamAccount($accountingAccount);
    }

    public static function getAccountFromIamAccountId($id) :?Accounts {
        return Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $id)
            ->first();
    }

    public static function getIamAccountFromInvoice(Invoices $invoice) : ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();

        return UserHelper::getAccountById($accountingAccount->iam_account_id);
    }

    public static function getAccountingAccount(int $accountId)
    {
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('iam_account_id', $accountId)->first();

        if(!$accountingAccount->distributor_id) {
            self::fixDistributorId($accountingAccount);
        }

        return $accountingAccount->fresh();
    }

    /**
     * Returns the IamAccount by looking at Accounting account
     *
     * @param Accounts $account
     * @return \NextDeveloper\IAM\Database\Models\Accounts
     */
    public static function getIamAccount(Accounts $account) : ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        return \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->iam_account_id)
            ->first();
    }

    /**
     * Returns the Accounting account of the Iam Account
     *
     * @param \NextDeveloper\IAM\Database\Models\Accounts|null $account
     * @return Accounts
     */
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

    public static function getCustomerProvider(Accounts $accounts) : ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $accounts->iam_account_id)
            ->first();

        if($iamAccount->common_country_id != null) {
            Log::info(__METHOD__ . '| Customer does not have a country id. Returning null as provider.');
            return null;
        }

        $provider = null;

        //  We will find the country of the account and then we will find the provider for that country.
        if($iamAccount->common_country_id) {
            $country = Countries::where('id', $iamAccount->common_country_id)->first();
            $providers = config('leo.providers.zones');

            if(array_key_exists(strtolower($country->code), $providers)) {
                $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', config('leo.providers.zones.' . strtolower($country->code). '.distributor'))
                    ->first();
            }
        }

        if(!$provider) {
            $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', config('leo.providers.zones.global.distributor'))
                ->first();
        }

        if(!$provider) {
            throw new \Exception('Cannot find the provider. Please update your configuration for provider.');
        }

        Log::info(__METHOD__ . '| Selecting the provider as ' . $provider->name);

        return $provider;
    }
}

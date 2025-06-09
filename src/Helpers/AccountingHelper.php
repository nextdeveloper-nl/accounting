<?php

namespace Helpers;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Database\Models\Countries;
use NextDeveloper\Commons\Helpers\CountryHelper;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Partnership\Helpers\PartnerHelper;

class AccountingHelper
{
    public static function getPaymentGatewayOfDistributor(Accounts $account)
    {
        return \NextDeveloper\Accounting\Database\Models\PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $account->id)
            ->where('is_active', true)
            ->first();
    }

    public static function getDistributorAccount(Accounts $account) {
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
            Log::info(__METHOD__ . '| Distributor ID is already set. No need to fix it.');
            return $account;
        }

        //  First we need to understand where the customer is actually from
        $iamAccount = self::getIamAccount($account);
        $country = CountryHelper::getCountryById($iamAccount->common_country_id);

        $provider = null;

        //  If the country is not set, we will use the global provider because we dont know where the customer is from.
        if(!$country) {
            $defaultProviderId = config('leo.providers.zones.global');
            $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $defaultProviderId)
                ->first();
        } else {
            //  If we know where the customer is then we should assign the related distributor
            //  For temporary we are assigning the default distributor

            $defaultProviderId = config('leo.providers.zones.global');
            $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $defaultProviderId)
                ->first();
        }

        $partner = PartnerHelper::getPartnerByIamAccount($provider);

        $account->updateQuietly([
            'distributor_id'    =>  $partner->id
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
        $accountingAccount = self::getAccountingAccount($invoice->accounting_account_id);

        return UserHelper::getAccountById($accountingAccount->iam_account_id);
    }

    public static function getAccountingAccount(int $accountId)
    {
        $accountingAccount = Accounts::where('id', $accountId)->first();

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

    public static function getCustomerProvider(Accounts $accounts) : \NextDeveloper\IAM\Database\Models\Accounts
    {
        $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $accounts->iam_account_id)
            ->first();

        if($iamAccount->common_country_id != null) {
            Log::info(__METHOD__ . '| Customer does not have a country id. Using the global provider.');
        }

        $provider = null;

        //  We will find the country of the account and then we will find the provider for that country.
        if($iamAccount->common_country_id) {
            $country = Countries::where('id', $iamAccount->common_country_id)->first();
            $providers = config('leo.providers.zones');

            if(array_key_exists(strtolower($country->code), $providers)) {
                $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', config('leo.providers.zones.' . strtolower($country->code)))
                    ->first();
            }
        }

        if(!$provider) {
            $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', config('leo.providers.zones.global'))
                ->first();
        }

        if(!$provider) {
            throw new \Exception('Cannot find the provider. Please update your configuration for provider.');
        }

        Log::info(__METHOD__ . '| Selecting the provider as ' . $provider->name);

        return $provider;
    }
}

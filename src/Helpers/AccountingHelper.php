<?php

namespace Helpers;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Database\Models\Countries;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingHelper
{
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
        return Accounts::where('id', $accountId)->first();
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

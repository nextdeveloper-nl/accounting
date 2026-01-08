<?php

namespace NextDeveloper\Accounting\Helpers;

use App\Envelopes\CRM\Accounts\AssignedAsAccountManager;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\AccountPartnerLogs;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PartnerAssignments;
use NextDeveloper\Commons\Database\Models\Countries;
use NextDeveloper\Commons\Helpers\CountryHelper;
use NextDeveloper\Communication\Helpers\Communicate;
use NextDeveloper\IAM\Database\Models\AccountsPerspective;
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
        $envelope = new AssignedAsAccountManager(
            $salesPartnerOwner,
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
            'sales_partner_id' => $myAccountingAccount->id
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

    /**
     * @param Accounts $account
     * @return mixed
     */
    public static function getDistributorAccount(Accounts $account)
    {
        //  We are trying to find the accounting_account of the distributor
        $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->distributor_id)
            ->first();

        if (!$distributorAccount) {
            self::fixDistributorId($account);

            $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $account->distributor_id)
                ->first();
        }

        return $distributorAccount;
    }

    /**
     * @param Accounts $account
     * @return mixed
     */
    public static function getIntegratorAccount(Accounts $account)
    {
        //  We are trying to find the accounting_account of the distributor
        $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->integrator_partner_id)
            ->first();

        return $distributorAccount;
    }

    /**
     * @param Accounts $account
     * @return mixed
     */
    public static function getResellerAccount(Accounts $account)
    {
        //  We are trying to find the accounting_account of the distributor
        $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->sales_partner_id)
            ->first();

        return $distributorAccount;
    }

    /**
     * @param Accounts $account
     * @return mixed
     */
    public static function getAffiliateAccount(Accounts $account)
    {
        //  We are trying to find the accounting_account of the distributor
        $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->affiliate_partner_id)
            ->first();

        return $distributorAccount;
    }

    /**
     * Here we are trying to find the suitable distributor for the customer and then assign the customer
     * to the related distributor
     *
     * @param Accounts $account
     * @return Accounts
     */
    public static function fixDistributorId(Accounts $account): Accounts
    {
        if ($account->distributor_id) {
            //  Not using this because it create infinite loop
            //$distributorAccount = self::getDistributorAccount($account);

            $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $account->distributor_id)
                ->first();

            if (!$distributorAccount) {
                Log::info(__METHOD__ . '| Distributor ID is not valid. Fixing it.');
            } else {
                Log::info(__METHOD__ . '| Distributor ID is valid. No need to fix it.');
                return $account;
            }
        }

        //  First we need to understand where the customer is actually from
        $iamAccount = self::getIamAccount($account);

        if (!$iamAccount) {
            Log::error(__METHOD__ . '| Cannot find the IAM account for the accounting account: ' . $account->id);
            return $account;
        }

        self::assignDistributorByCountry($account, $iamAccount);

        return $account->fresh();
    }

    public static function getIamAccountFromContract(Contracts $contract): ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $contract->accounting_account_id)
            ->first();

        return self::getIamAccount($accountingAccount);
    }

    public static function getAccountFromIamAccountId($id): ?Accounts
    {
        return Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $id)
            ->first();
    }

    public static function getIamAccountFromInvoice(Invoices $invoice): ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();

        return UserHelper::getAccountById($accountingAccount->iam_account_id);
    }

    public static function getAccountingAccount(int $accountId)
    {
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)->where('iam_account_id', $accountId)->first();

        if (!$accountingAccount->distributor_id) {
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
    public static function getIamAccount(Accounts $account): ?\NextDeveloper\IAM\Database\Models\Accounts
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
    public static function getAccount(\NextDeveloper\IAM\Database\Models\Accounts $account = null): Accounts
    {
        if (!$account)
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

    public static function getCustomerProvider(Accounts $accounts): ?\NextDeveloper\IAM\Database\Models\Accounts
    {
        $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $accounts->iam_account_id)
            ->first();

        if ($iamAccount->common_country_id == null) {
            Log::info(__METHOD__ . '| Customer does not have a country id. Returning null as provider.');
            return null;
        }

        $provider = null;

        //  We will find the country of the account and then we will find the provider for that country.
        if ($iamAccount->common_country_id) {
            $country = Countries::where('id', $iamAccount->common_country_id)->first();
            $providers = config('leo.providers.zones');

            if (array_key_exists(strtolower($country->code), $providers)) {
                $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', config('leo.providers.zones.' . strtolower($country->code) . '.distributor'))
                    ->first();
            }
        }

        if (!$provider) {
            $provider = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', config('leo.providers.zones.global.distributor'))
                ->first();
        }

        if (!$provider) {
            throw new \Exception('Cannot find the provider. Please update your configuration for provider.');
        }

        Log::info(__METHOD__ . '| Selecting the provider as ' . $provider->name);

        return $provider;
    }

    /**
     * Assigns distributor based on the account's country or uses global fallback
     *
     * @param Accounts $account
     * @param \NextDeveloper\IAM\Database\Models\Accounts $iamAccount
     * @return void
     */
    private static function assignDistributorByCountry(Accounts $account, \NextDeveloper\IAM\Database\Models\Accounts $iamAccount): void
    {
        $country = null;

        if ($iamAccount->common_country_id) {
            $country = CountryHelper::getCountryById($iamAccount->common_country_id);
        }

        if (!$country) {
            Log::info(__METHOD__ . '| Customer (' . $iamAccount->name . ' | ' . $iamAccount->uuid . ') does not have a country id. Using the global provider.');
        }

        //  If the country is not set, we will use the global provider because we don't know where the customer is from.
        if (!$country) {
            $defaultProviderId = config('leo.providers.zones.global.distributor');
        } else {
            //  If we know where the customer is, then we should assign the related distributor
            $defaultProviderId = config('leo.providers.zones.' . strtolower($country->code) . '.distributor');

            if (!$defaultProviderId) {
                $defaultProviderId = config('leo.providers.zones.global.distributor');
            }
        }

        $provider = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $defaultProviderId)
            ->first();

        if (!$provider) {
            Log::error(__METHOD__ . '| Cannot find provider account for IAM account ID: ' . $defaultProviderId);
            return;
        }

        $account->updateQuietly([
            'distributor_id' => $provider->id
        ]);

    }

    /**
     * Sets distribution partner and sales partner for the customer based on the provider account
     *
     * @param Accounts $provider
     * @param \NextDeveloper\IAM\Database\Models\Accounts $customer
     * @return void
     */
    public static function setDistributionPartner(
        Accounts $provider,
        \NextDeveloper\IAM\Database\Models\Accounts|AccountsPerspective $customer
    ): void {
        // get Customer's accounting
        $customerAccount = self::getAccountFromIamAccountId($customer->id);

        if (!$customerAccount) {
            Log::error(__METHOD__ . '| Cannot find accounting account for IAM account ID: ' . $customer->id);
            return;
        }

        if ($provider->distributor_id) {
            $customerAccount->updateQuietly([
                'distributor_id' => $provider->distributor_id,
                'sales_partner_id' => $provider->id
            ]);
        }
    }

    /**
     * Returns the accounting account by id
     *
     * @param int|string $accountId
     * @return Accounts|null
     */
    public static function getAccountingAccountById(int|string $accountId): ?Accounts
    {
        $account = Accounts::withoutGlobalScope(AuthorizationScope::class);

        if (is_string($accountId)) {
            $account = $account->where('uuid', $accountId);
        } else {
            $account = $account->where('id', $accountId);
        }

        return $account->first();
    }

    /**
     * Get the current partner assignment for an account based on a partner type
     *
     * @param Accounts $account The accounting account to check
     * @param string $type The partner type: 'distributor', 'integrator', or 'reseller'
     * @return PartnerAssignments|null
     */
    public static function getCurrentPartner(Accounts $account, string $type = 'integrator'): ?PartnerAssignments
    {
        // 1. Map partner type to the corresponding column name
        $partnerIdColumn = match ($type) {
            'distributor' => 'distributor_id',
            'integrator' => 'integrator_partner_id',
            'reseller' => 'sales_partner_id',
            default => null,
        };

        // 2. If the type is not valid, return null
        if (!$partnerIdColumn) {
            Log::warning(__METHOD__ . '| Invalid partner type provided: ' . $type);
            return null;
        }

        // 3. If the account does not have the related partner id, return null
        $partnerId = $account->{$partnerIdColumn};
        if (!$partnerId) {
            return null;
        }

        // 4. Try to find an existing active partner assignment
        $partnerAssignment = PartnerAssignments::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $account->id)
            ->where('type', $type)
            ->whereNull('finished_at')
            ->orderBy('started_at', 'desc')
            ->first();

        // 5. If no assignment exists, create a new one
        if (!$partnerAssignment) {
            $accountId = $account->id;

            $partnerAssignment = UserHelper::runAsAdmin(function () use ($accountId, $partnerId, $type) {
                $newAssignment = PartnerAssignments::create([
                    'accounting_account_id' => $accountId,
                    'type' => $type,
                    'new_partner_id' => $partnerId,
                    'started_at' => now(),
                ]);

                Log::info(__METHOD__ . '| Created new partner assignment for account ID: ' . $accountId . ' with partner type: ' . $type);

                return $newAssignment;
            });
        }

        return $partnerAssignment;
    }
}

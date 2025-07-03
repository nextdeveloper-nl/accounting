<?php
namespace NextDeveloper\Accounting\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Database\Models\Countries;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\RoleHelper;
use NextDeveloper\IAM\Helpers\UserHelper;

class AssignPartners extends Command {
    /**
     * @var string
     */
    protected $signature = 'nextdeveloper:assign-partners';

    /**
     * @var string
     */
    protected $description = 'Migrates partnership information from partners module to accounting';

    //  php artisan leo:bind-events

    /**
     * @return void
     */
    public function handle() {
        $this->line('Starting to search for partners.');
        Log::info(__METHOD__ . ' | Starting to search for partners.');

        UserHelper::setAdminAsCurrentUser();

        $accountingIds = Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->distinct('accounting_account_id')
            ->pluck('accounting_account_id');

        foreach ($accountingIds as $accountingId) {
            $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $accountingId)
                ->first();

            //  1)  Find the default distributor and assign it as the distributor for accounting account
            //  2)  Find the default support company and assign it as the integrator company

            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('id', $accountingAccount->iam_account_id)
                ->first();

            $country = Countries::where('code', config('leo.default_country_code'))->first();

            if(!$iamAccount->common_country_id) {
                $iamAccount->updateQuietly([
                    'common_country_id' => $country->id,
                ]);

                $iamAccount = $iamAccount->fresh();
            }

            if($iamAccount->id != $country->id)
                $country = Countries::where('id', $iamAccount->common_country_id)->first();

            $countryCode = $iamAccount->common_country_code;
            $countryCode = strtolower($countryCode);

            if(!array_key_exists($countryCode, config('leo.providers.zones')))
                $countryCode = 'global';

            $distributor = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', config('leo.providers.zones.' . $countryCode . '.distributor'))
                ->first();

            $integrator = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', config('leo.providers.zones.' . $countryCode . '.integrator_partner'))
                ->first();

            $reseller = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', config('leo.providers.zones.' . $countryCode . '.sales_partner'))
                ->first();

            $affiliate = Accounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', config('leo.providers.zones.' . $countryCode . '.affiliate_partner'))
                ->first();


            if(!$accountingAccount->distributor_id)
                $accountingAccount->distributor_id = $distributor->id;

            if(!$accountingAccount->integrator_partner_id)
                $accountingAccount->integrator_partner_id = $integrator->id;

            if(!$accountingAccount->affiliate_partner_id)
                $accountingAccount->affiliate_partner_id = $affiliate->id;

            if(!$accountingAccount->sales_partner_id)
                $accountingAccount->sales_partner_id = $reseller->id;

            try {
                $accountingAccount->unsetEventDispatcher();
                $accountingAccount->save();
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                dd($e);
            }

            $this->line($distributor-> id . ' / ' . $reseller->id . ' / ' . $affiliate->id . ' / ' . $integrator->id);
            $this->line('Updated accounting account with id: ' . $accountingAccount->id);
        }
    }
}

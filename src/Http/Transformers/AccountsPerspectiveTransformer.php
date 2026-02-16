<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\AccountsPerspective;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractAccountsPerspectiveTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * Class AccountsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AccountsPerspectiveTransformer extends AbstractAccountsPerspectiveTransformer
{

    /**
     * @param AccountsPerspective $model
     *
     * @return array
     */
    public function transform(AccountsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('AccountsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        $account = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $model->id)
            ->first();

        $distributor = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->distributor_id)
            ->first();

        $integrator = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->integrator_partner_id)
            ->first();

        $salesPartner = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->sales_partner_id)
            ->first();

        $affiliate = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $account->affiliate_partner_id)
            ->first();

        $transformed['distributor_id']  = $distributor ? $distributor->uuid : null;
        $transformed['distributor_partner'] = $distributor ? $distributor->name : null;
        $transformed['integrator_partner_id'] = $integrator ? $integrator->uuid : null;
        $transformed['integrator_partner'] = $integrator ? $integrator->name : null;
        $transformed['sales_partner_id'] = $salesPartner ? $salesPartner->uuid : null;
        $transformed['sales_partner'] = $salesPartner ? $salesPartner->name : null;
        $transformed['affiliate_partner_id'] = $affiliate ? $affiliate->uuid : null;
        $transformed['affiliate_partner'] = $affiliate ? $affiliate->uuid : null;

        $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $model->iam_account_id)
            ->withTrashed()
            ->first();

        $transformed['accounting_code'] = 'YS-' . $iamAccount->id;

        Cache::set(
            CacheHelper::getKey('AccountsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        if(class_exists('\NextDeveloper\CRM\Database\Models\AccountsPerspective')) {
            if($distributor) {
                //  Trying to find the distributor crm id
                $iamAccountDistributor = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', $distributor->iam_account_id)
                    ->first();

                $crmAccountDistributor = \NextDeveloper\CRM\Database\Models\AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
                    ->where('iam_account_id', $iamAccountDistributor->id)
                    ->first();

                $transformed['distributor_crm_account_id'] = $crmAccountDistributor->uuid;
                $transformed['distributor_crm_account_name'] = $crmAccountDistributor->name;
            } else {
                $transformed['distributor_crm_account_id'] = null;
                $transformed['distributor_crm_account_name'] = null;
            }

            if($integrator) {
                //  Trying to find the integrator crm id
                $iamAccountIntegrator = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', $integrator->iam_account_id)
                    ->first();

                $crmAccountIntegrator = \NextDeveloper\CRM\Database\Models\AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
                    ->where('iam_account_id', $iamAccountIntegrator->id)
                    ->first();

                $transformed['integrator_crm_account_id'] = $crmAccountIntegrator->uuid;
                $transformed['integrator_crm_account_name'] = $crmAccountIntegrator->name;
            } else {
                $transformed['integrator_crm_account_id'] = null;
                $transformed['integrator_crm_account_name'] = null;
            }

            if($salesPartner) {
                //  Trying to find the sales partner crm id
                $iamAccountSalesPartner = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', $salesPartner->iam_account_id)
                    ->first();

                $crmAccountSalesPartner = \NextDeveloper\CRM\Database\Models\AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
                    ->where('iam_account_id', $iamAccountSalesPartner->id)
                    ->first();

                $transformed['sales_partner_crm_account_id'] = $crmAccountSalesPartner->uuid;
                $transformed['sales_partner_crm_account_name'] = $crmAccountSalesPartner->name;
            } else {
                $transformed['sales_partner_crm_account_id'] = null;
                $transformed['sales_partner_crm_account_name'] = null;
            }

            if($affiliate) {
                //  Trying to find the affiliate partner crm id
                $iamAccountAffiliate = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', $affiliate->iam_account_id)
                    ->first();

                $crmAccountAffiliate = \NextDeveloper\CRM\Database\Models\AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
                    ->where('iam_account_id', $iamAccountAffiliate->id)
                    ->first();

                $transformed['affiliate_partner_crm_account_id'] = $crmAccountAffiliate->uuid;
                $transformed['affiliate_partner_crm_account_name'] = $crmAccountAffiliate->name;
            } else {
                $transformed['affiliate_partner_crm_account_id'] = null;
                $transformed['affiliate_partner_crm_account_name'] = null;
            }
        }

        return $transformed;
    }
}

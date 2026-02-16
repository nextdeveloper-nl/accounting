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

        $distributor = AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $model->distributor_id)
            ->first();

        $integrator = AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $model->integrator_partner_id)
            ->first();

        $salesPartner = AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $model->sales_partner_id)
            ->first();

        $affiliate = AccountsPerspective::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $model->affiliate_partner_id)
            ->first();

        $transformed['distributor_id']  = $distributor ? $distributor->uuid : null;
        $transformed['distributor_partner'] = $distributor ? $distributor->name : null;
        $transformed['integrator_partner_id'] = $integrator ? $integrator->uuid : null;
        $transformed['integrator_partner'] = $integrator ? $integrator->name : null;
        $transformed['sales_partner_id'] = $salesPartner ? $salesPartner->uuid : null;
        $transformed['sales_partner'] = $salesPartner ? $salesPartner->name : null;
        $transformed['affiliate_partner_id'] = $affiliate ? $affiliate->uuid : null;
        $transformed['affiliate_partner'] = $affiliate ? $affiliate->name : null;

        $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $model->iam_account_id)
            ->withTrashed()
            ->first();

        $transformed['accounting_code'] = 'YS-' . $iamAccount->id;

        Cache::set(
            CacheHelper::getKey('AccountsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

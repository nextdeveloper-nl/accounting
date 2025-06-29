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
        $transformed['integrator_partner_id'] = $integrator ? $integrator->uuid : null;
        $transformed['sales_partner_id'] = $salesPartner ? $salesPartner->uuid : null;
        $transformed['affiliate_partner_id'] = $affiliate ? $affiliate->uuid : null;

        Cache::set(
            CacheHelper::getKey('AccountsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

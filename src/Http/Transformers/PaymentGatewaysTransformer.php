<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPaymentGatewaysTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class PaymentGatewaysTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PaymentGatewaysTransformer extends AbstractPaymentGatewaysTransformer
{

    /**
     * @param PaymentGateways $model
     *
     * @return array
     */
    public function transform(PaymentGateways $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PaymentGateways', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PaymentGateways', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

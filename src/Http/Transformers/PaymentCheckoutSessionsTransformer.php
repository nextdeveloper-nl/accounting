<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPaymentCheckoutSessionsTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class PaymentCheckoutSessionsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PaymentCheckoutSessionsTransformer extends AbstractPaymentCheckoutSessionsTransformer
{

    /**
     * @param PaymentCheckoutSessions $model
     *
     * @return array
     */
    public function transform(PaymentCheckoutSessions $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PaymentCheckoutSessions', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PaymentCheckoutSessions', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

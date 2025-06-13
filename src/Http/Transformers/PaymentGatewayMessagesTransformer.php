<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\PaymentGatewayMessages;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPaymentGatewayMessagesTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class PaymentGatewayMessagesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PaymentGatewayMessagesTransformer extends AbstractPaymentGatewayMessagesTransformer
{

    /**
     * @param PaymentGatewayMessages $model
     *
     * @return array
     */
    public function transform(PaymentGatewayMessages $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PaymentGatewayMessages', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PaymentGatewayMessages', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

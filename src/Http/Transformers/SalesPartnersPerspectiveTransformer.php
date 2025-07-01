<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\SalesPartnersPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractSalesPartnersPerspectiveTransformer;

/**
 * Class SalesPartnersPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class SalesPartnersPerspectiveTransformer extends AbstractSalesPartnersPerspectiveTransformer
{

    /**
     * @param SalesPartnersPerspective $model
     *
     * @return array
     */
    public function transform(SalesPartnersPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('SalesPartnersPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('SalesPartnersPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

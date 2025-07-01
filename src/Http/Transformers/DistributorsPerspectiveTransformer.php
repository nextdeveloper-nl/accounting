<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\DistributorsPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractDistributorsPerspectiveTransformer;

/**
 * Class DistributorsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class DistributorsPerspectiveTransformer extends AbstractDistributorsPerspectiveTransformer
{

    /**
     * @param DistributorsPerspective $model
     *
     * @return array
     */
    public function transform(DistributorsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('DistributorsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('DistributorsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

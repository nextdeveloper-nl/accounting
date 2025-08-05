<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnershipsTransformer;

/**
 * Class PartnershipsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnershipsTransformer extends AbstractPartnershipsTransformer
{

    /**
     * @param Partnerships $model
     *
     * @return array
     */
    public function transform(Partnerships $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Partnerships', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Partnerships', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

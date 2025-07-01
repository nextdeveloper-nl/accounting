<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\AffiliatesPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractAffiliatesPerspectiveTransformer;

/**
 * Class AffiliatesPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AffiliatesPerspectiveTransformer extends AbstractAffiliatesPerspectiveTransformer
{

    /**
     * @param AffiliatesPerspective $model
     *
     * @return array
     */
    public function transform(AffiliatesPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('AffiliatesPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('AffiliatesPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

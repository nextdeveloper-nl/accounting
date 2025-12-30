<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\PartnershipsPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnershipsPerspectiveTransformer;

/**
 * Class PartnershipsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnershipsPerspectiveTransformer extends AbstractPartnershipsPerspectiveTransformer
{

    /**
     * @param PartnershipsPerspective $model
     *
     * @return array
     */
    public function transform(PartnershipsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PartnershipsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PartnershipsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

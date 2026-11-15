<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\PartnershipZones;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnershipZonesTransformer;

/**
 * Class PartnershipZonesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnershipZonesTransformer extends AbstractPartnershipZonesTransformer
{

    /**
     * @param PartnershipZones $model
     *
     * @return array
     */
    public function transform(PartnershipZones $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PartnershipZones', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PartnershipZones', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

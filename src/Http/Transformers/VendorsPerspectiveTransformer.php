<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\VendorsPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractVendorsPerspectiveTransformer;

/**
 * Class VendorsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class VendorsPerspectiveTransformer extends AbstractVendorsPerspectiveTransformer
{

    /**
     * @param VendorsPerspective $model
     *
     * @return array
     */
    public function transform(VendorsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('VendorsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('VendorsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

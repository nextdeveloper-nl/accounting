<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnershipRolesTransformer;

/**
 * Class PartnershipRolesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnershipRolesTransformer extends AbstractPartnershipRolesTransformer
{

    /**
     * @param PartnershipRoles $model
     *
     * @return array
     */
    public function transform(PartnershipRoles $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PartnershipRoles', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PartnershipRoles', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

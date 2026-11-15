<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\PartnershipRoleAssignments;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnershipRoleAssignmentsTransformer;

/**
 * Class PartnershipRoleAssignmentsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnershipRoleAssignmentsTransformer extends AbstractPartnershipRoleAssignmentsTransformer
{

    /**
     * @param PartnershipRoleAssignments $model
     *
     * @return array
     */
    public function transform(PartnershipRoleAssignments $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PartnershipRoleAssignments', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PartnershipRoleAssignments', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

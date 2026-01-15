<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\PartnerAssignments;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnerAssignmentsTransformer;

/**
 * Class PartnerAssignmentsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnerAssignmentsTransformer extends AbstractPartnerAssignmentsTransformer
{

    /**
     * @param PartnerAssignments $model
     *
     * @return array
     */
    public function transform(PartnerAssignments $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PartnerAssignments', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PartnerAssignments', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\PartnerPrograms;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnerProgramsTransformer;

/**
 * Class PartnerProgramsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnerProgramsTransformer extends AbstractPartnerProgramsTransformer
{

    /**
     * @param PartnerPrograms $model
     *
     * @return array
     */
    public function transform(PartnerPrograms $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PartnerPrograms', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PartnerPrograms', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

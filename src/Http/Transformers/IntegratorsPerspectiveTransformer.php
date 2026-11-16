<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Helpers\PartnerDirectory;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\IntegratorsPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractIntegratorsPerspectiveTransformer;

/**
 * Class IntegratorsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class IntegratorsPerspectiveTransformer extends AbstractIntegratorsPerspectiveTransformer
{

    /**
     * @param IntegratorsPerspective $model
     *
     * @return array
     */
    public function transform(IntegratorsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('IntegratorsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            //  Old cache entries still contain the private fields: strip them here as well.
            return PartnerDirectory::stripPrivateFields($transformed);
        }

        $transformed = parent::transform($model);

        //  Partner directory entry: never expose credit balance, tax numbers or trade registration data.
        $transformed = PartnerDirectory::stripPrivateFields($transformed);

        Cache::set(
            CacheHelper::getKey('IntegratorsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

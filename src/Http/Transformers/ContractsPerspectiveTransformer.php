<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\ContractsPerspective;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractContractsPerspectiveTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class ContractsPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class ContractsPerspectiveTransformer extends AbstractContractsPerspectiveTransformer
{

    /**
     * @param ContractsPerspective $model
     *
     * @return array
     */
    public function transform(ContractsPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('ContractsPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('ContractsPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

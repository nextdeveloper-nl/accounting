<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\ContractItems;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractContractItemsTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class ContractItemsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class ContractItemsTransformer extends AbstractContractItemsTransformer
{

    /**
     * @param ContractItems $model
     *
     * @return array
     */
    public function transform(ContractItems $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('ContractItems', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('ContractItems', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

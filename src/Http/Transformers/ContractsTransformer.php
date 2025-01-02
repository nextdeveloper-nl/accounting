<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractContractsTransformer;

/**
 * Class ContractsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class ContractsTransformer extends AbstractContractsTransformer
{

    /**
     * @param Contracts $model
     *
     * @return array
     */
    public function transform(Contracts $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Contracts', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Contracts', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractTransactionsTransformer;
use NextDeveloper\Commons\Common\Cache\CacheHelper;

/**
 * Class TransactionsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class TransactionsTransformer extends AbstractTransactionsTransformer
{

    /**
     * @param Transactions $model
     *
     * @return array
     */
    public function transform(Transactions $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Transactions', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Transactions', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

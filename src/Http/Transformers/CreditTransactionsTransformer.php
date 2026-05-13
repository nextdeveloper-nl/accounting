<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\CreditTransactions;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractCreditTransactionsTransformer;

/**
 * Class CreditTransactionsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class CreditTransactionsTransformer extends AbstractCreditTransactionsTransformer {

    /**
     * @param CreditTransactions $model
     *
     * @return array
     */
    public function transform(CreditTransactions $model) {
        $transformed = Cache::get(
            CacheHelper::getKey('CreditTransactions', $model->uuid, 'Transformed')
        );

        if($transformed)
            return $transformed;

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('CreditTransactions', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

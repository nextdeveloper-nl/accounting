<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\CreditCards;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractCreditCardsTransformer;

/**
 * Class CreditCardsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class CreditCardsTransformer extends AbstractCreditCardsTransformer
{

    /**
     * @param CreditCards $model
     *
     * @return array
     */
    public function transform(CreditCards $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('CreditCards', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('CreditCards', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

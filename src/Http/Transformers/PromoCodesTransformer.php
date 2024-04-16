<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\PromoCodes;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPromoCodesTransformer;

/**
 * Class PromoCodesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PromoCodesTransformer extends AbstractPromoCodesTransformer
{

    /**
     * @param PromoCodes $model
     *
     * @return array
     */
    public function transform(PromoCodes $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('PromoCodes', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('PromoCodes', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

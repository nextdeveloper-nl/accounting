<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\WeeklyPaidInvoicesPerformance;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractWeeklyPaidInvoicesPerformanceTransformer;

/**
 * Class WeeklyPaidInvoicesPerformanceTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class WeeklyPaidInvoicesPerformanceTransformer extends AbstractWeeklyPaidInvoicesPerformanceTransformer
{

    /**
     * @param WeeklyPaidInvoicesPerformance $model
     *
     * @return array
     */
    public function transform(WeeklyPaidInvoicesPerformance $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('WeeklyPaidInvoicesPerformance', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('WeeklyPaidInvoicesPerformance', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

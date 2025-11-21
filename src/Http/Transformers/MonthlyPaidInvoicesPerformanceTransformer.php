<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\MonthlyPaidInvoicesPerformance;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractMonthlyPaidInvoicesPerformanceTransformer;

/**
 * Class MonthlyPaidInvoicesPerformanceTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class MonthlyPaidInvoicesPerformanceTransformer extends AbstractMonthlyPaidInvoicesPerformanceTransformer
{

    /**
     * @param MonthlyPaidInvoicesPerformance $model
     *
     * @return array
     */
    public function transform(MonthlyPaidInvoicesPerformance $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('MonthlyPaidInvoicesPerformance', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('MonthlyPaidInvoicesPerformance', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

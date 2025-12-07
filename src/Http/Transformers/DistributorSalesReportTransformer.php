<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\DistributorSalesReport;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractDistributorSalesReportTransformer;

/**
 * Class DistributorSalesReportTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class DistributorSalesReportTransformer extends AbstractDistributorSalesReportTransformer
{

    /**
     * @param DistributorSalesReport $model
     *
     * @return array
     */
    public function transform(DistributorSalesReport $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('DistributorSalesReport', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('DistributorSalesReport', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

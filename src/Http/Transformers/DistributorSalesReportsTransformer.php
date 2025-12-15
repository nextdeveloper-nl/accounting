<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\DistributorSalesReports;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractDistributorSalesReportsTransformer;

/**
 * Class DistributorSalesReportsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class DistributorSalesReportsTransformer extends AbstractDistributorSalesReportsTransformer
{

    /**
     * @param DistributorSalesReports $model
     *
     * @return array
     */
    public function transform(DistributorSalesReports $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('DistributorSalesReports', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('DistributorSalesReports', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

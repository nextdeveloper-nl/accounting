<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\InvoiceCommissions;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractInvoiceCommissionsTransformer;

/**
 * Class InvoiceCommissionsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class InvoiceCommissionsTransformer extends AbstractInvoiceCommissionsTransformer
{

    /**
     * @param InvoiceCommissions $model
     *
     * @return array
     */
    public function transform(InvoiceCommissions $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('InvoiceCommissions', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('InvoiceCommissions', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

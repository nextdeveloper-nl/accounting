<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractInvoiceItemsTransformer;

/**
 * Class InvoiceItemsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class InvoiceItemsTransformer extends AbstractInvoiceItemsTransformer
{

    /**
     * @param InvoiceItems $model
     *
     * @return array
     */
    public function transform(InvoiceItems $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('InvoiceItems', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('InvoiceItems', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

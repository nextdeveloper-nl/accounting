<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\InvoicesPerspective;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractInvoicesPerspectiveTransformer;

/**
 * Class InvoicesPerspectiveTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class InvoicesPerspectiveTransformer extends AbstractInvoicesPerspectiveTransformer
{

    /**
     * @param InvoicesPerspective $model
     *
     * @return array
     */
    public function transform(InvoicesPerspective $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('InvoicesPerspective', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('InvoicesPerspective', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

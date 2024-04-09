<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractInvoicesTransformer;

/**
 * Class InvoicesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class InvoicesTransformer extends AbstractInvoicesTransformer
{

    /**
     * @param Invoices $model
     *
     * @return array
     */
    public function transform(Invoices $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Invoices', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Invoices', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\InvoicesPerspective;
use NextDeveloper\Commons\Database\Models\States;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractInvoicesPerspectiveTransformer;
use NextDeveloper\Commons\Http\Transformers\StatesTransformer;

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
//        $transformed = Cache::get(
//            CacheHelper::getKey('InvoicesPerspective', $model->uuid, 'Transformed')
//        );

//        if($transformed) {
//            return $transformed;
//        }

        $transformed = parent::transform($model);

        $states = States::where('object_type', Invoices::class)
            ->where('object_id', $model->id)
            ->get();

        foreach ($states as $state) {
            $transformed['states']['data'][] = (new StatesTransformer())->transform($state);
        }

//        Cache::set(
//            CacheHelper::getKey('InvoicesPerspective', $model->uuid, 'Transformed'),
//            $transformed
//        );

        return $transformed;
    }
}

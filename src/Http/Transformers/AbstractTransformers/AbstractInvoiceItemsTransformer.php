<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class InvoiceItemsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractInvoiceItemsTransformer extends AbstractTransformer
{

    /**
     * @param InvoiceItems $model
     *
     * @return array
     */
    public function transform(InvoiceItems $model)
    {
                        $commonCurrencyId = \NextDeveloper\Commons\Database\Models\Currencies::where('id', $model->common_currency_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'object_type'  =>  $model->object_type,
            'object_id'  =>  $model->object_id,
            'quantity'  =>  $model->quantity,
            'unit_price'  =>  $model->unit_price,
            'total_price'  =>  $model->total_price,
            'common_currency_id'  =>  $commonCurrencyId ? $commonCurrencyId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            'iam_account_ig'  =>  $model->iam_account_ig,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE






}

<?php

namespace NextDeveloper\Accounting\Http\Requests\InvoiceItems;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoiceItemsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_type' => 'nullable|string',
        'object_id' => 'nullable',
        'quantity' => 'integer',
        'unit_price' => 'nullable',
        'total_price' => 'nullable',
        'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
        'iam_account_ig' => 'integer',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
<?php

namespace NextDeveloper\Accounting\Http\Requests\InvoiceItems;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoiceItemsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_type' => 'required|string',
        'object_id' => 'required',
        'quantity' => 'integer',
        'unit_price' => 'required',
        'total_price' => 'required',
        'common_currency_id' => 'required|exists:common_currencies,uuid|uuid',
        'iam_account_ig' => 'integer',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
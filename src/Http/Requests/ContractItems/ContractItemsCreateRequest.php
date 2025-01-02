<?php

namespace NextDeveloper\Accounting\Http\Requests\ContractItems;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContractItemsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_type' => 'required|string',
        'object_id' => 'required',
        'discount_ratio' => '',
        'fixed_price' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
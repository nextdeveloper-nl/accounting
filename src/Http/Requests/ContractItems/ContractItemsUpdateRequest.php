<?php

namespace NextDeveloper\Accounting\Http\Requests\ContractItems;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class ContractItemsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'object_type' => 'nullable|string',
        'object_id' => 'nullable',
        'discount_ratio' => '',
        'fixed_price' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
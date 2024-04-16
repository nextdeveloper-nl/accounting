<?php

namespace NextDeveloper\Accounting\Http\Requests\PromoCodes;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PromoCodesCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'code' => 'required|string',
        'value' => 'required|integer',
        'common_currency_id' => 'required|exists:common_currencies,uuid|uuid',
        'gift_code_data' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
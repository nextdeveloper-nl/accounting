<?php

namespace NextDeveloper\Accounting\Http\Requests\PaymentGateways;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PaymentGatewaysCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string',
        'gateway' => 'required|string',
        'is_active' => 'boolean',
        'common_country_id' => 'required|exists:common_countries,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
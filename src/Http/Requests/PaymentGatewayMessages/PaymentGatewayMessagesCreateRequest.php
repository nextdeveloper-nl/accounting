<?php

namespace NextDeveloper\Accounting\Http\Requests\PaymentGatewayMessages;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PaymentGatewayMessagesCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'message_identifier' => 'nullable|string',
        'message' => 'nullable|string',
        'accounting_payment_gateway_id' => 'required|exists:accounting_payment_gateways,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
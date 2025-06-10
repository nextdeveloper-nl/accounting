<?php

namespace NextDeveloper\Accounting\Http\Requests\PaymentCheckoutSessions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PaymentCheckoutSessionsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_payment_gateway_id' => 'nullable|exists:accounting_payment_gateways,uuid|uuid',
        'payment_data' => 'nullable',
        'session_data' => 'nullable',
        'is_invalidated' => 'boolean',
        'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
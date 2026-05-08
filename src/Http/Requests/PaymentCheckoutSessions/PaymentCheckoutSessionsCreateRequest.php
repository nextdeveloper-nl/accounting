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
            'accounting_payment_gateway_id' => 'required|exists:accounting_payment_gateways,uuid|uuid',
        'accounting_invoice_id' => 'required|exists:accounting_invoices,uuid|uuid',
        'payment_data' => 'nullable',
        'session_data' => 'nullable',
        'is_invalidated' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
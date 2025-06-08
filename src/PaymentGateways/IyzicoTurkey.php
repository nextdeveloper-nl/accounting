<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;

class IyzicoTurkey implements PaymentGatewaysInterface
{
    private $gateway;

    public function __construct(PaymentGateways $gateway)
    {
        $this->gateway = $gateway;
    }

    public function createCheckoutSession(Invoices $invoice): PaymentCheckoutSessions
    {
        trigger_error('IyzicoTurkey payment gateway is not implemented yet.', E_USER_WARNING);
    }
}

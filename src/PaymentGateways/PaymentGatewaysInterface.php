<?php

namespace Nextdeveloper\Accounting\PaymentGateways;

use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;

interface PaymentGatewaysInterface
{
    public function createCheckoutSession(Invoices $invoice): PaymentCheckoutSessions;
}

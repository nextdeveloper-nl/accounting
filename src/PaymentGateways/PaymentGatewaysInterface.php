<?php

namespace Nextdeveloper\Accounting\PaymentGateways;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;

interface PaymentGatewaysInterface
{
    public function getCheckoutSession(Accounts $account): PaymentCheckoutSessions;
}

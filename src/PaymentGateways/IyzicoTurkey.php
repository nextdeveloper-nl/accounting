<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use Omnipay\Iyzico\Gateway as IyzicoGateway;

class IyzicoTurkey extends IyzicoGateway implements PaymentGatewaysInterface
{
    private $gateway;

    public function __construct(PaymentGateways $gateway)
    {
        $this->gateway = $gateway;

        parent::__construct();
    }

    public function createCheckoutSession(Invoices $invoice): PaymentCheckoutSessions
    {
        trigger_error('IyzicoTurkey payment gateway is not implemented yet.', E_USER_WARNING);
    }

    public function getCheckoutSession(Accounts $account): PaymentCheckoutSessions
    {
        // TODO: Implement getCheckoutSession() method.
    }
}

<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use Helpers\InvoiceHelper;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Exceptions\CheckoutSessionException;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;

class Stripe implements PaymentGatewaysInterface
{
    private $gateway;

    public function __construct(PaymentGateways $gateway)
    {
        if($gateway->parameters['is_test']) {
            $this->gateway = new \Stripe\StripeClient($gateway->parameters['test_api_secret']);
        } else {
            $this->gateway = new \Stripe\StripeClient($gateway->parameters['live_api_secret']);
        }
    }

    public function createCheckoutSession(Invoices $invoice): \NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions
    {
        $lineItems = InvoiceItems::where('accounting_invoice_id', $invoice->id)->get();

        $invoice = InvoiceHelper::fixCurrencyCode($invoice);
        $invoice = InvoiceHelper::calculateByGateway($invoice->fresh());

//        if($lineItems->isEmpty()) {
//            throw new CheckoutSessionException('Cannot create checkout session, ' .
//                'since this invoice has no products inside. Please try to make wire transfer or consult ' .
//                'to your financial advisor.');
//        }

        $session = $this->gateway->checkout->sessions->create([
            'success_url' => 'https://example.com/success',
            //'line_items' =>$lineItems,
            'mode' => 'subscription',
        ]);

        dd($session);
    }
}

<?php

namespace Nextdeveloper\Accounting\PaymentGateways;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\Transactions;

interface PaymentGatewaysInterface
{
    public function getCheckoutSession(Accounts $account): PaymentCheckoutSessions;

    /**
     * Creates a payment link for the given invoice.
     *
     * @param Accounts $account The account associated with the invoice.
     * @param Invoices $invoice The invoice for which to create the payment link.
     * @return string|null The payment link URL, or null if creation failed.
     */
    public function createPaymentLink(Accounts $account, Invoices $invoice, Transactions $transaction): ?string;

    /**
     * Handles payment callback/webhook from the payment gateway.
     *
     * @param array $callbackData The callback data received from the payment gateway.
     * @param array $headers The HTTP headers from the webhook request (for signature validation)
     * @return array Returns an array with:
     *               - 'success' (bool): Whether the callback was processed successfully
     *               - 'invoice_id' (int|string|null): The invoice ID if payment was successful
     *               - 'transaction_id' (string|null): The transaction ID from the gateway
     *               - 'paid' (bool): Whether the payment was successful
     *               - 'message' (string|null): Any message about the processing
     */
    public function handleCallback(array $callbackData, array $headers = []): array;
}


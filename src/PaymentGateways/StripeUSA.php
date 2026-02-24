<?php

namespace NextDeveloper\Accounting\PaymentGateways;

use App\Products\Products;
use Illuminate\Support\Carbon;
use Log;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\Accounting\Services\InvoiceItemsService;
use NextDeveloper\Accounting\Services\InvoicesService;
use NextDeveloper\Accounting\Services\TransactionsService;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\IAM\Services\AccountsService;

class StripeUSA implements PaymentGatewaysInterface
{
    private $gateway;

    private $gatewayObject;

    /**
     * @var Accounts Accounting Account
     */
    private $gatewayOwner;

    // Stripe zero-decimal currencies
    // Source: https://stripe.com/docs/currencies#zero-decimal

    const ZERO_DECIMAL_CURRENCIES = [
        'BIF',
        'CLP',
        'DJF',
        'GNF',
        'JPY',
        'KMF',
        'KRW',
        'MGA',
        'PYG',
        'RWF',
        'UGX',
        'VND',
        'VUV',
        'XAF',
        'XOF',
        'XPF'
    ];

    public function __construct(PaymentGateways $gateway, Accounts $account)
    {
        $this->gatewayObject = $gateway;

        if ($gateway->parameters['is_test']) {
            $this->gateway = new \Stripe\StripeClient($gateway->parameters['test_api_secret']);
        } else {
            $this->gateway = new \Stripe\StripeClient($gateway->parameters['live_api_secret']);
        }

        $this->gatewayOwner = $account;
    }

    public function getCheckoutSession(Accounts $account): \NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions
    {
        $checkoutSession = PaymentCheckoutSessions::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $account->id)
            ->first();

        $iamAccount = AccountingHelper::getIamAccount($account);
        $iamUser = UserHelper::getAccountOwner($iamAccount);

        if (!$checkoutSession) {
            $stripeCustomer = $this->getStripeCustomer($account);

            //  Here this means that we dont have a checkout session. We need to create a checkout session with predefined Membership product
            $session = $this->gateway->checkout->sessions->create([
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
                'customer' => $stripeCustomer->id,
                'line_items' => [
                    [
                        'price' => config('leo.payment.stripe.default_membership_product_id'), // Assuming product_id is the Stripe price ID
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
            ]);

            $distributorAccount = AccountingHelper::getDistributorAccount($account);
            $gateway = AccountingHelper::getPaymentGatewayOfDistributor($distributorAccount);

            $checkoutSession = PaymentCheckoutSessions::create([
                'accounting_account_id' => $account->id,
                'accounting_payment_gateway_id' => $gateway->id,
                'session_data' => [
                    'id' => $session->id,
                    'customer' => $stripeCustomer->id,
                    'status' => $session->status,
                    'expires_at' => $session->expires_at,
                    'approval_url' => $session->url
                ]
            ]);
        }

        return $checkoutSession;
    }

    public function getStripeCustomer(Accounts $account)
    {
        $iamAccount = AccountingHelper::getIamAccount($account);
        $iamUser = UserHelper::getAccountOwner($iamAccount);

        $customers = $this->gateway->customers->search([
            'query' => "email:'{$iamUser->email}'",
        ]);

        if (!$customers->data || count($customers->data) == 0) {
            //  If we don't have a customer, we need to create one
            $customer = $this->gateway->customers->create([
                'email' => $iamUser->email,
                'name' => $iamUser->name,
                'metadata' => [
                    'account_id' => $account->id,
                    'iam_account_id' => $iamAccount->id,
                ],
            ]);

            return $customer;
        } else {
            //  If we have a customer, we will return the first one
            return $customers->data[0];
        }
    }

    /**
     * Creates a Stripe Payment Link for the given invoice.
     *
     * Flow:
     *  1. Guard: invoice already paid -> null
     *  2. Resolve currency & compute minor units (handling zero-decimal currencies)
     *  3. Build a one-off Price (ephemeral) then Payment Link
     *  4. Return Payment Link URL
     *
     * Returns null on: already paid, invalid currency, computed amount <= 0, below min (if configured), or Stripe error.
     *
     * @param Accounts $account
     * @param Invoices $invoice
     * @param Transactions $transaction
     * @return string|null Payment link URL or null when creation not possible.
     */
    public function createPaymentLink(Accounts $account, Invoices $invoice, Transactions $transaction): ?string
    {
        $isPaid = $invoice->is_paid;
        if ($isPaid) {
            Log::info(__METHOD__ . '::' . __LINE__ . ' - Invoice is already paid', ['invoice_id' => $invoice->id]);
            return null;
        }

        // get currency and amount from the invoice
        $currency = Currencies::where('id', $invoice->common_currency_id)
            ->first();

        if (!$currency) {
            Log::error(__METHOD__ . '::' . __LINE__ . ' - Currency not found', ['currency_code' => $invoice->common_currency_id]);
            return null;
        }

        $currencyCode = strtoupper($currency->code);
        $unitAmount = $this->convertToMinorUnits($invoice->amount, $currencyCode);

        if ($unitAmount <= 0) {
            Log::warning(__METHOD__ . '::' . __LINE__ . ' - Calculated unit amount is not positive', [
                'accounting_invoice_id' => $invoice->id,
                'raw_amount' => $invoice->amount,
                'unit_amount' => $unitAmount,
                'currency' => $currencyCode
            ]);
        }

        try {
            // create payment link for the invoice with safe integer amount
            $price = $this->gateway->prices->create([
                'unit_amount' => $unitAmount,
                'currency' => strtolower($currencyCode),
                'product_data' => [
                    'name' => "Invoice #" . now()->year . "-" .  $invoice->id,
                    'metadata' => [
                        'accounting_transaction_id' => $transaction->uuid,
                    ],
                ],
            ]);

            $paymentLink = $this->gateway->paymentLinks->create([
                'line_items' => [
                    [
                        'price' => $price->id,
                        'quantity' => 1,
                    ],
                ],
                'metadata' => [
                    'accounting_transaction_id' => $transaction->uuid,
                ],
            ]);

            return $paymentLink->url;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error(__METHOD__ . ' Stripe API error creating payment link', [
                'accounting_invoice_id' => $invoice->id,
                'stripe_error_type' => method_exists($e, 'getError') && $e->getError() ? $e->getError()->type : null,
                'stripe_error_code' => method_exists($e, 'getError') && $e->getError() ? $e->getError()->code : null,
                'message' => $e->getMessage(),
            ]);
            return null;
        } catch (\Throwable $e) {
            Log::error(__METHOD__ . ' Unexpected error creating payment link', [
                'accounting_invoice_id' => $invoice->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Convert a decimal amount to Stripe's expected minor unit integer safely.
     * Handles zero-decimal currencies and floating point precision issues.
     */
    private function convertToMinorUnits($amount, string $currencyCode): int
    {
        $zeroDecimalCurrencies = self::ZERO_DECIMAL_CURRENCIES;

        if (in_array($currencyCode, $zeroDecimalCurrencies)) {
            return (int) round($amount);
        }

        if (function_exists('bcmul')) {
            return (int) bcmul((string) $amount, '100', 0);
        }

        return (int) round(((float) $amount) * 100);
    }

    /**
     * Handle payment callback from Stripe
     *
     * @param array $callbackData The webhook data from Stripe
     * @param array $headers The HTTP headers from the webhook request (for signature validation)
     * @return array Result of callback processing
     */
    public function handleCallback(array $callbackData, array $headers = []): array
    {
        try {
            Log::info('Stripe callback received', ['data' => $callbackData]);

            // Stripe sends event type to identify the webhook
            $eventType = $callbackData['type'] ?? null;

            if (!$eventType) {
                return [
                    'success' => false,
                    'message' => 'No event type found in callback data'
                ];
            }

            // Handle different Stripe event types
            switch ($eventType) {
                case 'payment_intent.succeeded':
                case 'checkout.session.completed':
                    return $this->handleSuccessfulPayment($callbackData);

                case 'payment_intent.payment_failed':
                case 'checkout.session.expired':
                    return $this->handleFailedPayment($callbackData);

                case 'customer.subscription.created':
                    return $this->handleSubscriptionCreated($callbackData);

                case 'invoice.created':
                    return $this->handleInvoiceCreated($callbackData);

                case 'invoice.finalized':
                    return $this->handleInvoiceFinalized($callbackData);

                case 'invoice.paid':
                case 'invoice.payment_succeeded':
                    return $this->handleSuccessfulPayment($callbackData);

                default:
                    Log::info('Unhandled Stripe event type', ['type' => $eventType]);
                    return [
                        'success' => true,
                        'message' => 'Event type not processed: ' . $eventType
                    ];
            }

        } catch (\Exception $e) {
            if(config('app.debug')) {
                throw $e;
            }

            Log::error('Error processing Stripe callback', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'message' => 'Error processing callback: ' . $e->getMessage()
            ];
        }
    }

    private function handleInvoiceFinalized(array $callbackData): array
    {
        $data = $callbackData['data']['object'];

        $existingInvoice = Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->where('invoice_number', 'stripe_' . $data['id'])
            ->first();

        if(!$existingInvoice) {
            throw new \Exception('Invoice not found');
        }

        UserHelper::runAsAdmin(function () use ($existingInvoice) {
            $existingInvoice->update([
                'is_sealed' => true,
                'is_payable' => true,
            ]);
        });
    }

    private function handleInvoiceCreated(array $callbackData): array
    {
        $data = $callbackData['data']['object'];

        $existingInvoice = Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->where('invoice_number', 'stripe_' . $data['id'])
            ->first();

        if($existingInvoice) {
            return [
                'success' => true,
            ];
        }

        $customerEmail = $data['customer_email'] ?? null;

        if(!$customerEmail) {
            throw new \Exception('No customer email found in callback data');
        }

        $user = UserHelper::getWithEmail($customerEmail);
        $account = UserHelper::masterAccount($user);

        $accountingAccount = AccountingHelper::getAccountingAccount($account->id);

        //  WE ARE MAPPING THE CUSTOMER HERE FOR LATER USAGE
        $mapping = $accountingAccount->mapping;

        if(!$mapping) {
            UserHelper::runAsAdmin(function () use ($accountingAccount, $data) {
                $accountingAccount->update([
                    'mapping'   =>  [
                        'stripe'    =>  $data['customer']
                    ]
                ]);
            });
        } else {
            if(!array_key_exists('stripe', $mapping)) {
                UserHelper::runAsAdmin(function () use ($accountingAccount, $mapping, $data) {
                    $accountingAccount->update([
                        'mapping'   =>  array_merge(
                            $mapping, [
                            'stripe'    =>  $data['customer']
                            ]
                        )
                    ]);
                });
            }
        }

        //  CREATING THE INVOICE

        $gatewayOwner = UserHelper::getAccountById($this->gatewayOwner->iam_account_id);
        $gatewayUser = UserHelper::getAccountOwner($gatewayOwner->id);

        $createData = [
            'accounting_account_id' => $accountingAccount->id,
            'invoice_number' => 'stripe_' . $data['id'],
            'common_currency_id' => Currencies::where('code', 'ilike', $data['currency'])->first()->id,
            'exchange_rate' => [],
            'amount' => floatval($data['amount_paid']) / 100,
            //  We need to check this
            'vat' => 0,
            'is_paid' => false,
            'is_refund' => false,
            'is_payable' => false,
            'is_sealed' => false,
            'term_year' => Carbon::now()->year,
            'term_month' => Carbon::now()->month,
            'iam_account_id' => $gatewayOwner->id,
            'iam_user_id' => $gatewayUser->id,
        ];

        $invoice = null;

        UserHelper::runAsAdmin(function() use (&$invoice, $createData) {
            $invoice = InvoicesService::create($createData);
        });

        foreach ($data['lines']['data'] as $line) {
            $product = $line['pricing']['price_details']['product'];

            $availableProducts = Products::availableProducts();

            $productSold = null;

            foreach ($availableProducts as $availableProduct) {
                if ($product == $availableProduct::STRIPE_CODE) {
                    $productSold = $availableProduct;
                }
            }

            $invoiceItem = [
                'object_type'   =>  $productSold,
                'object_id'     =>  0,
                'quantity'      =>  $line['quantity'],
                'unit_price'    =>  $line['amount'] / 100,
                'common_currency_id'    =>  Currencies::where('code', 'ilike', $line['currency'])->first()->id,
                'iam_account_id' => $gatewayOwner->id,
                'accounting_invoice_id' => $invoice->id,
                'accounting_account_id' => $accountingAccount->id,
                'details'   =>  $line
            ];

            UserHelper::runAsAdmin(function() use (&$invoiceItem) {
                $invoiceItem = InvoiceItemsService::create($invoiceItem);
            });
        }

        return [
            'success'   =>  true
        ];
    }

    private function handleSubscriptionCreated(array $callbackData): array
    {
        $data = $eventData['data']['object'] ?? [];
    }

    /**
     * Handle successful payment from Stripe
     *
     * @param array $eventData
     * @return array
     */
    private function handleSuccessfulPayment(array $eventData): array
    {
        $data = $eventData['data']['object'] ?? [];

        $existingInvoice = Invoices::withoutGlobalScope(AuthorizationScope::class)
            ->where('invoice_number', 'stripe_' . $data['id'])
            ->first();

        $invoiceItems = InvoiceItems::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_invoice_id', $existingInvoice->id)
            ->get();

        $products = Products::availableProducts();

        foreach ($invoiceItems as $item) {
            foreach ($products as $product) {
                if($item['object_type'] == $product) {
                    $app = app($item['object_type']);
                    $account = AccountingHelper::getIamAccountFromInvoice($existingInvoice);
                    $app->subscribeFor($account);
                }
            }
        }

        // Extract transaction ID from metadata
        $transactionId = $data['metadata']['accounting_transaction_id'] ?? null;
        $paymentIntentId = $data['id'] ?? $data['payment_intent'] ?? null;

        if (!$transactionId) {
            $existingTransaction = Transactions::withoutGlobalScope(AuthorizationScope::class)
                ->where('conversation_identifier', $existingInvoice->invoice_number)
                ->first();

            if(!$existingTransaction) {
                //  We need to create the transaction here.
                UserHelper::runAsAdmin(function () use (&$transactionId, $data, $existingInvoice) {
                    $transaction = Transactions::create([
                        'accounting_invoice_id' => $existingInvoice->id,
                        'amount' => $existingInvoice->amount,
                        'common_currency_id' => $existingInvoice->common_currency_id,
                        'accounting_payment_gateway_id' => $this->gatewayObject->id,
                        'iam_account_id' => UserHelper::me()->id,
                        'accounting_account_id' => $existingInvoice->accounting_account_id,
                        'gateway_response'   =>  'invoice.payment_succeeded',
                        'conversation_identifier' => $existingInvoice->invoice_number,
                        'is_pending' => false
                    ]);

                    $transactionId = $transaction->id;
                });
            }

            $transactionId = $existingTransaction->id;
        }

        return [
            'success' => true,
            'accounting_transaction_id' => $transactionId,
            'transaction_id' => $paymentIntentId,
            'paid' => true,
            'payment_method' => 'stripe',
            'raw_data' => $eventData
        ];
    }

    /**
     * Handle failed payment from Stripe
     *
     * @param array $eventData
     * @return array
     */
    private function handleFailedPayment(array $eventData): array
    {
        $data = $eventData['data']['object'] ?? [];
        $transactionId = $data['metadata']['accounting_transaction_id'] ?? null;

        Log::info('Stripe payment failed', [
            'accounting_transaction_id' => $transactionId,
            'event_type' => $eventData['type'] ?? 'unknown'
        ]);

        return [
            'success' => true,
            'paid' => false,
            'accounting_transaction_id' => $transactionId,
            'message' => 'Payment failed or expired',
            'raw_data' => $eventData
        ];
    }
}

<?php

namespace NextDeveloper\Accounting\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Filters\InvoicesQueryFilter;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractInvoicesService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * This class is responsible from managing the data for Invoices
 *
 * Class InvoicesService.
 */
class InvoicesService extends AbstractInvoicesService
{
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
    public static function get(?InvoicesQueryFilter $filter = null, array $params = []): Collection|LengthAwarePaginator
    {
        return parent::get($filter, $params);
    }

    /*
     * Create a payment link for invoice
     */
    public static function createPaymentLink(Invoices $invoice): ?string
    {
        // Idempotent: if a link was already generated for this invoice, reuse it.
        if ($invoice->payment_link_url) {
            return $invoice->payment_link_url;
        }

        // get Accounting account
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();

        if (! $accountingAccount) {
            Log::error(__METHOD__.'::'.__LINE__.' - Accounting account not found', ['invoice_id' => $invoice->id]);

            return null;
        }

        // get distributor account
        $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $accountingAccount->distributor_id)
            ->first();

        if (! $distributorAccount) {
            Log::error(__METHOD__.'::'.__LINE__.' - Distributor account not found', ['invoice_id' => $invoice->id]);

            return null;
        }

        //  Prefer the Iyzico hosted link (IyziLink); fall back to Stripe when the
        //  distributor has no active Iyzico gateway configured.
        $gateways = PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $distributorAccount->id)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->where('name', 'iyzico-link')
                    ->orWhere('name', 'ilike', 'stripe-%');
            })
            ->get();

        $paymentGateway = $gateways->firstWhere('name', 'iyzico-link')
            ?? $gateways->first(fn ($gateway) => Str::contains($gateway->name, 'stripe'));

        if (! $paymentGateway) {
            Log::error(__METHOD__.'::'.__LINE__.' - Payment gateway not found', ['invoice_id' => $invoice->id, 'gateways' => ['iyzico-link', 'stripe-*']]);

            return null;
        }

        $class = $paymentGateway->gateway;

        if (! class_exists($class)) {
            Log::error(
                __METHOD__.'::'.__LINE__.' - Payment gateway class not found',
                [
                    'accounting_invoice_id' => $invoice->id,
                    'class' => $class,
                ],
            );

            return null;
        }

        // create transaction record
        $transaction = Transactions::withoutGlobalScope(AuthorizationScope::class)
            ->updateOrCreate([
                'accounting_invoice_id' => $invoice->id,
                'amount' => $invoice->amount,
                'common_currency_id' => $invoice->common_currency_id,
                'accounting_payment_gateway_id' => $paymentGateway->id,
                'iam_account_id' => $invoice->iam_account_id,
                'accounting_account_id' => $invoice->accounting_account_id,
            ], [
                'accounting_invoice_id' => $invoice->id,
                'amount' => $invoice->amount,
                'common_currency_id' => $invoice->common_currency_id,
                'accounting_payment_gateway_id' => $paymentGateway->id,
                'iam_account_id' => $invoice->iam_account_id,
                'accounting_account_id' => $invoice->accounting_account_id,
                'conversation_identifier' => 'inv-'.$invoice->id.'-'.time(),
                'is_pending' => true,
            ]);

        $transaction->fresh();

        $class = new $class($paymentGateway, $accountingAccount);

        $link = $class->createPaymentLink($accountingAccount, $invoice, $transaction);

        if ($link) {
            $invoice->payment_link_url = $link;
            $invoice->saveQuietly();
        }

        return $link;
    }
}

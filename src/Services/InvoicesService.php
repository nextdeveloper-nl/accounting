<?php

namespace NextDeveloper\Accounting\Services;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Filters\InvoicesQueryFilter;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractInvoicesService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * This class is responsible from managing the data for Invoices
 *
 * Class InvoicesService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class InvoicesService extends AbstractInvoicesService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
    public static function get(InvoicesQueryFilter $filter = null, array $params = []): \Illuminate\Database\Eloquent\Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return parent::get($filter, $params);
    }

    public static function create($data)
    {
        $invoices = parent::create($data);
        self::createPaymentLink($invoices);
        return $invoices;
    }

    public static function createPaymentLink(Invoices $invoice): ?string
    {
        // get Accounting account
        $accountingAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();

        if (!$accountingAccount) {
            Log::error(__METHOD__ . '::' . __LINE__ . ' - Accounting account not found', ['invoice_id' => $invoice->id]);
            return null;
        }

        // get distributor account
        $distributorAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $accountingAccount->distributor_id)
            ->first();

        if (!$distributorAccount) {
            Log::error(__METHOD__ . '::' . __LINE__ . ' - Distributor account not found', ['invoice_id' => $invoice->id]);
            return null;
        }

        $paymentGateway = PaymentGateways::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $distributorAccount->id)
            ->first();

        if (!$paymentGateway) {
            Log::error(__METHOD__ . '::' . __LINE__ . ' - Payment gateway not found', ['invoice_id' => $invoice->id]);
            return null;
        }

        $class = $paymentGateway->gateway;

        if (!class_exists($class)) {
            Log::error(
                __METHOD__ . '::' . __LINE__ . ' - Payment gateway class not found',
                [
                    'invoice_id' => $invoice->id,
                    'class' => $class,
                ],
            );
            return null;
        }

        $class = new $class($paymentGateway);

        $link = $class->createPaymentLink($accountingAccount, $invoice);

        if($link) {
            $invoice->payment_link_url = $link;
            $invoice->saveQuietly();
        }

        return $link;
    }
}

<?php

namespace NextDeveloper\Accounting\Helpers;

use Carbon\Carbon;
use Helpers\InvoiceHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\ContractItems;
use NextDeveloper\Accounting\Database\Models\ContractItemsPerspective;
use NextDeveloper\Accounting\Database\Models\Contracts;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Services\ContractsService;
use NextDeveloper\Commons\Database\GlobalScopes\LimitScope;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

class ContractHelper
{
    public const FIXED_DISCOUNT = 'fixed-discount';

    public const FIXED_PRICE = 'fixed-price';

    public const SIX_MONTHS = '6months';

    public const ONE_YEAR = '1year';

    public const TWO_YEAR = '2year';

    public const THREE_YEAR = '3year';

    public const FOUR_YEAR = '4year';

    public const FIVE_YEAR = '5year';

    public static function getContractById($contractId) :Contracts {
        if(Str::isUuid($contractId))
            return Contracts::where('uuid', $contractId)->first();

        return Contracts::where('id', $contractId)->first();
    }

    public static function createNewContractForCrmAccount(
        \NextDeveloper\CRM\Database\Models\Accounts $account,
        $from = null,
        $lenght = null
    ) {
        return self::createNewContract(
            AccountingHelper::getAccountFromCrmAccount($account)
        );
    }

    public static function createNewContract(Accounts $accountingAccount, $from = null, $lenght = null)
    {
        if(!$from)
            $from = now();

        if(!$lenght)
            $lenght = self::THREE_YEAR;

        $termStart = $from;
        $termEnd = null;

        switch ($lenght) {
            case self::SIX_MONTHS:
                $termEnd = $termStart->copy()->addMonths(6);
                break;
            case self::ONE_YEAR:
                $termEnd = $termStart->copy()->addMonths(12);
                break;
            case self::TWO_YEAR:
                $termEnd = $termStart->copy()->addMonths(24);
                break;
            case self::THREE_YEAR;
                $termEnd = $termStart->copy()->addMonths(36);
                break;
            case self::FOUR_YEAR;
                $termEnd = $termStart->copy()->addMonths(48);
                break;
            case self::FIVE_YEAR:
                $termEnd = $termStart->copy()->addMonths(60);
                break;
        }

        return ContractsService::create([
            'name'          =>  'Initial draft contract',
            'contract_type' =>  self::FIXED_DISCOUNT,
            'term_starts'   =>  $termStart,
            'term_end'      =>  $termEnd,
            'is_approved'   =>  false,
            'accounting_account_id' =>  $accountingAccount->id
        ]);
    }

    public function setLenght($contract = null, $lenght = self::THREE_YEAR) {
        if(!$contract) {
            return null;
        }

        $termEnd = null;

        switch ($lenght) {
            case self::SIX_MONTHS:
                $termEnd = $contract->term_starts->copy()->addMonths(6);
                break;
            case self::ONE_YEAR:
                $termEnd = $contract->term_starts->copy()->addMonths(12);
                break;
            case self::TWO_YEAR:
                $termEnd = $contract->term_starts->copy()->addMonths(24);
                break;
            case self::THREE_YEAR;
                $termEnd = $contract->term_starts->copy()->addMonths(36);
                break;
            case self::FOUR_YEAR;
                $termEnd = $contract->term_starts->copy()->addMonths(48);
                break;
            case self::FIVE_YEAR:
                $termEnd = $contract->term_starts->copy()->addMonths(60);
                break;
        }

        $contract->update([
            'term_ends' =>  $termEnd
        ]);

        $contract = $contract->fresh();

        return $contract;
    }

    /**
     * The signed contract item that covers this object during the given window, or null
     * when the object is billed at list price for that window.
     *
     * A contract counts when its term overlaps the window at all; the partial months at
     * the two ends of a term are handled by the coverage ratio, not by dropping them.
     * When more than one signed contract overlaps, the one that started last wins: that
     * is the renewal or the renegotiation of the older one.
     *
     * @param object $object The billed object, e.g. a virtual machine.
     */
    public static function getContractItemForWindow($object, Carbon $windowStart, Carbon $windowEnd)
    {
        return ContractItemsPerspective::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_type', get_class($object))
            ->where('object_id', $object->id)
            ->where('term_starts', '<=', $windowEnd)
            ->where('term_ends', '>=', $windowStart)
            ->where('is_signed', true)
            ->where('is_approved', true)
            ->orderBy('term_starts', 'desc')
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * How much of the window the contract term actually covers, between 0 and 1.
     *
     * A term that starts or ends inside the window only covers part of it. The price of
     * a contract item is a monthly price, so a month that is covered for a third is
     * worth a third of it — that way a twelve month term is paid exactly twelve times,
     * however the term sits between month boundaries.
     */
    public static function getCoverageRatio($contractItem, Carbon $windowStart, Carbon $windowEnd): float
    {
        $windowSeconds = $windowEnd->diffInSeconds($windowStart);

        if($windowSeconds <= 0) {
            return 0;
        }

        $coverStarts = $contractItem->term_starts->greaterThan($windowStart)
            ? $contractItem->term_starts->copy()
            : $windowStart->copy();

        $coverEnds = $contractItem->term_ends->lessThan($windowEnd)
            ? $contractItem->term_ends->copy()
            : $windowEnd->copy();

        if($coverEnds->lessThanOrEqualTo($coverStarts)) {
            return 0;
        }

        return min(1, $coverEnds->diffInSeconds($coverStarts) / $windowSeconds);
    }

    /**
     * Takes the part of an invoice line that a contract covers off the line. What the
     * contract covers is paid with the contract invoice, so billing it again month by
     * month would charge the customer twice. A fully covered line ends up at zero, a
     * line covered for a third keeps two thirds of its price.
     *
     * @param float $coverage Between 0 and 1, from getCoverageRatio().
     */
    public static function settleInvoiceItem(InvoiceItems $item, $contractItem, float $coverage): InvoiceItems
    {
        if($coverage <= 0) {
            return $item;
        }

        $details = $item->details ?? [];

        $details['contract_coverage'] = 'The contract ' . $contractItem->uuid . ' covers %'
            . round($coverage * 100, 2) . ' of this term. That part is paid with the contract, '
            . 'so only the rest of the term is invoiced here.';

        $item->update([
            'unit_price'    =>  $item->unit_price * (1 - $coverage),
            'details'       =>  $details
        ]);

        Log::info('[##COVERED BY CONTRACT##] ' . $details['contract_coverage']
            . ' Invoice item: ' . $item->uuid);

        return $item;
    }

    /**
     * Settles what is already invoiced for the objects of a contract. Called when a
     * contract becomes signed: the customer pays the contract itself, so the invoices
     * that were raised for those objects inside the term have to give that money back.
     *
     * Invoices the customer already paid are left alone; taking a paid invoice down to
     * zero would be a refund, and that is not a decision this code can make.
     *
     * @return array{items: int, invoices: int, paid: array<int, string>, skipped_paid: array<int, string>, skipped_sealed: array<int, string>}
     */
    public static function settleCoveredInvoiceItems(Contracts $contract): array
    {
        $summary = ['items' => 0, 'invoices' => 0, 'paid' => [], 'skipped_paid' => [], 'skipped_sealed' => []];

        if(!$contract->is_signed || !$contract->term_starts || !$contract->term_ends) {
            return $summary;
        }

        $items = ContractItems::withoutGlobalScope(AuthorizationScope::class)
            ->withoutGlobalScope(LimitScope::class)
            ->where('accounting_contract_id', $contract->id)
            ->get();

        if($items->isEmpty()) {
            return $summary;
        }

        //  Only the terms that are already invoiced can be settled, the future ones are
        //  simply never invoiced.
        $window = $contract->term_starts->copy()->startOfMonth();
        $lastWindow = min($contract->term_ends, Carbon::now())->copy()->startOfMonth();

        $touchedInvoices = [];

        while($window->lessThanOrEqualTo($lastWindow)) {
            $windowStart = $window->copy()->startOfMonth();
            $windowEnd = $window->copy()->endOfMonth();

            $coverage = self::getCoverageRatio($contract, $windowStart, $windowEnd);

            if($coverage <= 0) {
                $window->addMonth();
                continue;
            }

            $invoices = Invoices::withoutGlobalScope(AuthorizationScope::class)
                ->withoutGlobalScope(LimitScope::class)
                ->where('accounting_account_id', $contract->accounting_account_id)
                ->where('term_year', $windowStart->year)
                ->where('term_month', $windowStart->month)
                ->get();

            foreach ($invoices as $currentInvoice) {
                if($currentInvoice->is_paid) {
                    $summary['skipped_paid'][] = $currentInvoice->uuid;
                    continue;
                }

                /**
                 * A sealed invoice is issued and is not rewritten anymore. The invoice of
                 * the contract itself is sealed too, which is what keeps this from taking
                 * the contract down to zero with its own money.
                 */
                if($currentInvoice->is_sealed) {
                    $summary['skipped_sealed'][] = $currentInvoice->uuid;
                    continue;
                }

                foreach ($items as $contractItem) {
                    $invoiceItem = InvoiceItems::withoutGlobalScope(AuthorizationScope::class)
                        ->withoutGlobalScope(LimitScope::class)
                        ->where('accounting_invoice_id', $currentInvoice->id)
                        ->where('object_type', $contractItem->object_type)
                        ->where('object_id', $contractItem->object_id)
                        ->first();

                    if(!$invoiceItem || $invoiceItem->unit_price <= 0) {
                        continue;
                    }

                    self::settleInvoiceItem($invoiceItem, $contractItem, $coverage);

                    $summary['items']++;
                    $touchedInvoices[$currentInvoice->id] = $currentInvoice;
                }
            }

            $window->addMonth();
        }

        foreach ($touchedInvoices as $invoice) {
            InvoiceHelper::updateInvoiceAmount($invoice);

            $invoice = $invoice->fresh();

            //  Nothing is left to collect on this invoice, so it is settled.
            if($invoice->amount <= 0 && !$invoice->is_paid) {
                $invoice->update(['is_paid' => true]);

                $summary['paid'][] = $invoice->uuid;
            }

            $summary['invoices']++;
        }

        return $summary;
    }
}

<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Database\Filters\InvoicesQueryFilter;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractInvoicesService;

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
        $invoices = parent::get($filter, $params);

        foreach ($invoices as $invoice) {
            $invoice->append(self::getInvoiceReport($invoice));
        }

        return $invoices;
    }

    public static function getInvoiceReport(Invoices $invoice) : array {
        $items = InvoiceItems::where('accounting_invoice_id', $invoice->id)
            ->orderBy('object_type', 'asc')
            ->orderBy('object_id', 'asc')
            ->get();

        foreach ($items as $item) {

        }
    }
}

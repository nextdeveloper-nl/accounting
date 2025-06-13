<?php

namespace NextDeveloper\Accounting\Services;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use NextDeveloper\Accounting\Database\Filters\InvoiceItemsPerspectiveQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractInvoiceItemsPerspectiveService;

/**
 * This class is responsible from managing the data for InvoiceItemsPerspective
 *
 * Class InvoiceItemsPerspectiveService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class InvoiceItemsPerspectiveService extends AbstractInvoiceItemsPerspectiveService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
    public static function get(InvoiceItemsPerspectiveQueryFilter $filter = null, array $params = []): Collection|LengthAwarePaginator
    {
        return parent::get($filter, $params);
    }
}

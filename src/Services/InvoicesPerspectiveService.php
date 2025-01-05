<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Database\Filters\InvoicesPerspectiveQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractInvoicesPerspectiveService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * This class is responsible from managing the data for InvoicesPerspective
 *
 * Class InvoicesPerspectiveService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class InvoicesPerspectiveService extends AbstractInvoicesPerspectiveService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
    public static function get(InvoicesPerspectiveQueryFilter $filter = null, array $params = []): Collection|LengthAwarePaginator
    {
        return parent::get($filter, $params);
    }
}
<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Database\Filters\InvoicesQueryFilter;
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
        return parent::get($filter, $params);
    }
}

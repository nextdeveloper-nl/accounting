<?php

namespace NextDeveloper\Accounting\Http\Requests\WeeklyPaidInvoicesPerformance;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class WeeklyPaidInvoicesPerformanceCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'week_start' => 'nullable|date',
'week_end' => 'nullable|date',
'week_number' => 'nullable|string',
'count' => 'nullable|integer',
'total_amount' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
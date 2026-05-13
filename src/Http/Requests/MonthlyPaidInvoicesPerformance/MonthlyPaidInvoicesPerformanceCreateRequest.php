<?php

namespace NextDeveloper\Accounting\Http\Requests\MonthlyPaidInvoicesPerformance;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class MonthlyPaidInvoicesPerformanceCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'month_start' => 'nullable|date',
'month_end' => 'nullable|date',
'month_name' => 'nullable|string',
'month_code' => 'nullable|string',
'common_currency_id' => 'nullable|exists:common_currencies,uuid|uuid',
'count' => 'nullable|integer',
'total_amount' => 'nullable',
'avg_amount' => 'nullable',
'min_amount' => 'nullable',
'max_amount' => 'nullable',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
<?php

namespace NextDeveloper\Accounting\Http\Requests\CreditCards;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class CreditCardsUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'nullable|string',
        'type' => 'nullable|string',
        'cc_holder_name' => 'nullable|string',
        'cc_number' => 'nullable|string',
        'cc_month' => 'nullable|string',
        'cc_year' => 'nullable|string',
        'cc_cvv' => 'nullable|string',
        'is_default' => 'boolean',
        'is_valid' => 'boolean',
        'is_active' => 'boolean',
        'is_3d_secure' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
<?php

namespace NextDeveloper\Accounting\Http\Requests\CreditCards;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class CreditCardsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'string',
        'type' => 'nullable|string',
        'cc_holder_name' => 'required|string',
        'cc_number' => 'required|string',
        'cc_month' => 'required|string',
        'cc_year' => 'required|string',
        'cc_cvv' => 'required|string',
        'is_default' => 'boolean',
        'is_valid' => 'boolean',
        'is_active' => 'boolean',
        'is_3d_secure' => 'boolean',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

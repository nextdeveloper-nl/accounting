<?php

namespace NextDeveloper\Accounting\Http\Requests\Partnerships;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnershipsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules()
    {
        return [
            'accounting_account_id' => 'nullable|exists:accounting_accounts,uuid|uuid',
        'partner_code' => 'nullable|string',
        'is_brand_ambassador' => 'boolean',
        'customer_count' => 'integer',
        'level' => 'integer',
        'reward_points' => 'integer',
        'boosts' => 'nullable',
        'mystery_box' => 'nullable',
        'badges' => 'nullable',
        'is_approved' => 'boolean',
        'technical_capabilities' => 'nullable',
        'industry' => 'nullable|string',
        'sector_focus' => 'nullable',
        'special_interest' => 'nullable',
        'compliance_certifications' => 'nullable',
        'target_group' => 'nullable',
        'meeting_link' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
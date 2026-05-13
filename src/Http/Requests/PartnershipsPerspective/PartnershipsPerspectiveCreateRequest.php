<?php

namespace NextDeveloper\Accounting\Http\Requests\PartnershipsPerspective;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnershipsPerspectiveCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'name' => 'nullable|string',
'partner_code' => 'nullable|string',
'is_brand_ambassador' => 'nullable|boolean',
'customer_count' => 'nullable|integer',
'level' => 'nullable|integer',
'reward_points' => 'nullable|integer',
'boosts' => 'nullable',
'mystery_box' => 'nullable',
'badges' => 'nullable',
'is_approved' => 'nullable|boolean',
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
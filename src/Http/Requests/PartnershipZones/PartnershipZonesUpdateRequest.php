<?php

namespace NextDeveloper\Accounting\Http\Requests\PartnershipZones;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnershipZonesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'partnership_id' => 'nullable|exists:accounting_partnerships,uuid|uuid',
            'partnership_role_id' => 'nullable|exists:accounting_partnership_roles,uuid|uuid',
            'common_country_id' => 'nullable|exists:common_countries,uuid|uuid',
            'common_city_id' => 'nullable|exists:common_cities,uuid|uuid',
            'region' => 'nullable|string',
            'is_exclusive' => 'nullable|boolean',
            'priority' => 'nullable|integer',
            'status' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

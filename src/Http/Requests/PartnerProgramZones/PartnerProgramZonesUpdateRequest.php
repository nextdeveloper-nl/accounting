<?php

namespace NextDeveloper\Accounting\Http\Requests\PartnerProgramZones;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnerProgramZonesUpdateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'partner_program_id' => 'nullable|exists:accounting_partner_programs,uuid|uuid',
            'common_country_id' => 'nullable|exists:common_countries,uuid|uuid',
            'partnership_role_id' => 'nullable|exists:accounting_partnership_roles,uuid|uuid',
            'default_partnership_id' => 'nullable|exists:accounting_partnerships,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

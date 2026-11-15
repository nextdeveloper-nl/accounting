<?php

namespace NextDeveloper\Accounting\Http\Requests\PartnerPrograms;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class PartnerProgramsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'responsible_email' => 'nullable|string',
            'notification_webhook' => 'nullable|string',
            'branding' => 'nullable',
            'default_country_code' => 'nullable|string',
            'max_hierarchy_depth' => 'nullable|integer',
            'status' => 'nullable|string',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

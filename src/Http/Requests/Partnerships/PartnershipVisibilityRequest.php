<?php

namespace NextDeveloper\Accounting\Http\Requests\Partnerships;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

/**
 * Who may see a partner in the partner lists: private, customers or public.
 */
class PartnershipVisibilityRequest extends AbstractFormRequest
{
    /**
     * @return array
     */
    public function rules() {
        return [
            'visibility' => 'required|string|in:private,customers,public',
        ];
    }
}

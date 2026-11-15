<?php

namespace NextDeveloper\Accounting\Http\Requests\InvoiceCommissions;

use NextDeveloper\Commons\Http\Requests\AbstractFormRequest;

class InvoiceCommissionsCreateRequest extends AbstractFormRequest
{

    /**
     * @return array
     */
    public function rules() {
        return [
            'accounting_invoice_id' => 'required|exists:accounting_invoices,uuid|uuid',
            'partnership_role_id' => 'required|exists:accounting_partnership_roles,uuid|uuid',
            'partnership_id' => 'required|exists:accounting_partnerships,uuid|uuid',
            'level' => 'nullable|integer',
            'commission_invoice_id' => 'nullable|exists:accounting_invoices,uuid|uuid',
        ];
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

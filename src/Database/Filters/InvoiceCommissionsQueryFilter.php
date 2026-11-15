<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class InvoiceCommissionsQueryFilter extends AbstractQueryFilter
{

    /**
    * @var Builder
    */
    protected $builder;

    public function level($value)
    {
        return $this->builder->where('level', '=', $value);
    }

    public function createdAtStart($date)
    {
        return $this->builder->where( 'created_at', '>=', $date );
    }

    public function createdAtEnd($date)
    {
        return $this->builder->where( 'created_at', '<=', $date );
    }

    //  This is an alias function of createdAt
    public function created_at_start($value)
    {
        return $this->createdAtStart($value);
    }

    //  This is an alias function of createdAt
    public function created_at_end($value)
    {
        return $this->createdAtEnd($value);
    }

    public function updatedAtStart($date)
    {
        return $this->builder->where( 'updated_at', '>=', $date );
    }

    public function updatedAtEnd($date)
    {
        return $this->builder->where( 'updated_at', '<=', $date );
    }

    //  This is an alias function of updatedAt
    public function updated_at_start($value)
    {
        return $this->updatedAtStart($value);
    }

    //  This is an alias function of updatedAt
    public function updated_at_end($value)
    {
        return $this->updatedAtEnd($value);
    }

    public function deletedAtStart($date)
    {
        return $this->builder->where( 'deleted_at', '>=', $date );
    }

    public function deletedAtEnd($date)
    {
        return $this->builder->where( 'deleted_at', '<=', $date );
    }

    //  This is an alias function of deletedAt
    public function deleted_at_start($value)
    {
        return $this->deletedAtStart($value);
    }

    //  This is an alias function of deletedAt
    public function deleted_at_end($value)
    {
        return $this->deletedAtEnd($value);
    }

    public function tenantAccountId($value)
    {
        $ref = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('tenant_account_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?tenant_account_id=<uuid> works too
    public function tenant_account_id($value)
    {
        return $this->tenantAccountId($value);
    }

    public function accountingInvoiceId($value)
    {
        $ref = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('accounting_invoice_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?accounting_invoice_id=<uuid> works too
    public function accounting_invoice_id($value)
    {
        return $this->accountingInvoiceId($value);
    }

    public function partnershipRoleId($value)
    {
        $ref = \NextDeveloper\Accounting\Database\Models\PartnershipRoles::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('partnership_role_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?partnership_role_id=<uuid> works too
    public function partnership_role_id($value)
    {
        return $this->partnershipRoleId($value);
    }

    public function partnershipId($value)
    {
        $ref = \NextDeveloper\Accounting\Database\Models\Partnerships::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('partnership_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?partnership_id=<uuid> works too
    public function partnership_id($value)
    {
        return $this->partnershipId($value);
    }

    public function commissionInvoiceId($value)
    {
        $ref = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('commission_invoice_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?commission_invoice_id=<uuid> works too
    public function commission_invoice_id($value)
    {
        return $this->commissionInvoiceId($value);
    }

    public function iamAccountId($value)
    {
        $ref = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('iam_account_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?iam_account_id=<uuid> works too
    public function iam_account_id($value)
    {
        return $this->iamAccountId($value);
    }

    public function iamUserId($value)
    {
        $ref = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('iam_user_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?iam_user_id=<uuid> works too
    public function iam_user_id($value)
    {
        return $this->iamUserId($value);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}

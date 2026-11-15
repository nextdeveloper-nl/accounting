<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class PartnershipRolesQueryFilter extends AbstractQueryFilter
{

    /**
    * @var Builder
    */
    protected $builder;

    public function slug($value)
    {
        return $this->builder->where('slug', 'ilike', '%' . $value . '%');
    }

    public function name($value)
    {
        return $this->builder->where('name', 'ilike', '%' . $value . '%');
    }

    public function description($value)
    {
        return $this->builder->where('description', 'ilike', '%' . $value . '%');
    }

    public function commissionRate($value)
    {
        return $this->builder->where('commission_rate', '=', $value);
    }

    public function parentOverrideRate($value)
    {
        return $this->builder->where('parent_override_rate', '=', $value);
    }

    public function commissionBasis($value)
    {
        return $this->builder->where('commission_basis', 'ilike', '%' . $value . '%');
    }

    public function priority($value)
    {
        return $this->builder->where('priority', '=', $value);
    }

    public function allowsMultiplePerCustomer($value)
    {
        return $this->builder->where('allows_multiple_per_customer', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function isDefaultForSignupCode($value)
    {
        return $this->builder->where('is_default_for_signup_code', filter_var($value, FILTER_VALIDATE_BOOLEAN));
    }

    public function status($value)
    {
        return $this->builder->where('status', 'ilike', '%' . $value . '%');
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

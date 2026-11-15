<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class AccountPartnersQueryFilter extends AbstractQueryFilter
{

    /**
    * @var Builder
    */
    protected $builder;

    public function startedAtStart($date)
    {
        return $this->builder->where( 'started_at', '>=', $date );
    }

    public function startedAtEnd($date)
    {
        return $this->builder->where( 'started_at', '<=', $date );
    }

    //  This is an alias function of startedAt
    public function started_at_start($value)
    {
        return $this->startedAtStart($value);
    }

    //  This is an alias function of startedAt
    public function started_at_end($value)
    {
        return $this->startedAtEnd($value);
    }

    public function finishedAtStart($date)
    {
        return $this->builder->where( 'finished_at', '>=', $date );
    }

    public function finishedAtEnd($date)
    {
        return $this->builder->where( 'finished_at', '<=', $date );
    }

    //  This is an alias function of finishedAt
    public function finished_at_start($value)
    {
        return $this->finishedAtStart($value);
    }

    //  This is an alias function of finishedAt
    public function finished_at_end($value)
    {
        return $this->finishedAtEnd($value);
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

    public function accountingAccountId($value)
    {
        $ref = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($ref) {
            return $this->builder->where('accounting_account_id', '=', $ref->id);
        }
    }

    //  Snake case alias, so that ?accounting_account_id=<uuid> works too
    public function accounting_account_id($value)
    {
        return $this->accountingAccountId($value);
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

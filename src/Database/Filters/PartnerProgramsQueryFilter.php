<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class PartnerProgramsQueryFilter extends AbstractQueryFilter
{

    /**
    * @var Builder
    */
    protected $builder;

    public function responsibleEmail($value)
    {
        return $this->builder->where('responsible_email', 'ilike', '%' . $value . '%');
    }

    public function notificationWebhook($value)
    {
        return $this->builder->where('notification_webhook', 'ilike', '%' . $value . '%');
    }

    public function defaultCountryCode($value)
    {
        return $this->builder->where('default_country_code', 'ilike', '%' . $value . '%');
    }

    public function maxHierarchyDepth($value)
    {
        return $this->builder->where('max_hierarchy_depth', '=', $value);
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

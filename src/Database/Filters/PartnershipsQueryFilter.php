<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class PartnershipsQueryFilter extends AbstractQueryFilter
{

    /**
    * @var Builder
    */
    protected $builder;

    public function partnerCode($value)
    {
        return $this->builder->where('partner_code', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of partnerCode
    public function partner_code($value)
    {
    return $this->partnerCode($value);
    }

    public function industry($value)
    {
        return $this->builder->where('industry', 'ilike', '%' . $value . '%');
    }


    public function meetingLink($value)
    {
        return $this->builder->where('meeting_link', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of meetingLink
    public function meeting_link($value)
    {
    return $this->meetingLink($value);
    }

    public function customerCount($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
           $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('customer_count', $operator, $value);
    }

        //  This is an alias function of customerCount
    public function customer_count($value)
    {
    return $this->customerCount($value);
    }

    public function level($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
           $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('level', $operator, $value);
    }


    public function rewardPoints($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
           $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('reward_points', $operator, $value);
    }

        //  This is an alias function of rewardPoints
    public function reward_points($value)
    {
    return $this->rewardPoints($value);
    }

    public function isBrandAmbassador($value)
    {
        return $this->builder->where('is_brand_ambassador', $value);
    }

        //  This is an alias function of isBrandAmbassador
    public function is_brand_ambassador($value)
    {
    return $this->isBrandAmbassador($value);
    }

    public function isApproved($value)
    {
        return $this->builder->where('is_approved', $value);
    }

        //  This is an alias function of isApproved
    public function is_approved($value)
    {
    return $this->isApproved($value);
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

    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
        }


    public function accountingAccountId($value)
    {
            $accountingAccount = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($accountingAccount) {
            return $this->builder->where('accounting_account_id', '=', $accountingAccount->id);
        }
        }

        //  This is an alias function of accountingAccount
    public function accounting_account_id($value)
    {
    return $this->accountingAccount($value);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Revoked partnerships are history: unless the caller asks for a status explicitly (?status=revoked, or any other
     * value), the list does not contain them. Single partnerships (show by id) are not affected.
     */
    public function apply(Builder $builder)
    {
        $builder = parent::apply($builder);

        if (!array_key_exists('status', $this->filters())) {
            $builder->where($builder->getModel()->getTable() . '.status', '!=', 'revoked');
        }

        return $builder;
    }

    //  Multi-tenant partnership filters. Placed below the marker on purpose: the generator does not know them yet.

    /**
     * ?status=pending|active|rejected|revoked (exact match)
     */
    public function status($value)
    {
        return $this->builder->where('status', '=', $value);
    }

    /**
     * ?visibility=private|customers|public (exact match)
     */
    public function visibility($value)
    {
        return $this->builder->where('visibility', '=', $value);
    }

    /**
     * ?tenant_id=<uuid of the IAM account that owns the program>
     */
    public function tenantId($value)
    {
        $tenant = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScope(
            \NextDeveloper\IAM\Database\Scopes\AuthorizationScope::class
        )->where('uuid', $value)->first();

        //  Unknown tenant: return nothing instead of ignoring the filter and returning everything.
        return $this->builder->where('tenant_account_id', '=', $tenant ? $tenant->id : 0);
    }

    //  Snake case alias, so that ?tenant_id=<uuid> works too
    public function tenant_id($value)
    {
        return $this->tenantId($value);
    }

    /**
     * ?descendants_of=<uuid of a partnership> returns the whole sub-partner tree below it (every level, not the
     * partnership itself), so a UI can load one subtree instead of the full partner list.
     *
     * The root is looked up WITH the viewer's role scope: a partnership the viewer cannot see gives an empty result,
     * so this filter cannot be used to read the tree below someone else's partner.
     */
    public function descendantsOf($value)
    {
        $root = \NextDeveloper\Accounting\Database\Models\Partnerships::where('uuid', $value)->first();

        if (!$root || !$root->path) {
            return $this->builder->whereRaw('1 = 0');
        }

        //  path is the materialised list of ancestor ids + own id (e.g. 5.9.14), so descendants start with "<path>.".
        return $this->builder
            ->where('tenant_account_id', $root->tenant_account_id)
            ->where('path', 'like', $root->path . '.%');
    }

    public function descendants_of($value)
    {
        return $this->descendantsOf($value);
    }

    /**
     * ?parent_partnership_id=<uuid of the parent partnership>
     */
    public function parentPartnershipId($value)
    {
        $parent = \NextDeveloper\Accounting\Database\Models\Partnerships::withoutGlobalScope(
            \NextDeveloper\IAM\Database\Scopes\AuthorizationScope::class
        )->where('uuid', $value)->first();

        return $this->builder->where('parent_partnership_id', '=', $parent ? $parent->id : 0);
    }

    public function parent_partnership_id($value)
    {
        return $this->parentPartnershipId($value);
    }



















}

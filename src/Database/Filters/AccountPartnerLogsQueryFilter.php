<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class AccountPartnerLogsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function reason($value)
    {
        return $this->builder->where('reason', 'ilike', '%' . $value . '%');
    }

    
    public function startedAtStart($date)
    {
        return $this->builder->where('started_at', '>=', $date);
    }

    public function startedAtEnd($date)
    {
        return $this->builder->where('started_at', '<=', $date);
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
        return $this->builder->where('finished_at', '>=', $date);
    }

    public function finishedAtEnd($date)
    {
        return $this->builder->where('finished_at', '<=', $date);
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
        return $this->builder->where('created_at', '>=', $date);
    }

    public function createdAtEnd($date)
    {
        return $this->builder->where('created_at', '<=', $date);
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
        return $this->builder->where('updated_at', '>=', $date);
    }

    public function updatedAtEnd($date)
    {
        return $this->builder->where('updated_at', '<=', $date);
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
        return $this->builder->where('deleted_at', '>=', $date);
    }

    public function deletedAtEnd($date)
    {
        return $this->builder->where('deleted_at', '<=', $date);
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
    
    public function oldPartnerId($value)
    {
            $oldPartner = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($oldPartner) {
            return $this->builder->where('old_partner_id', '=', $oldPartner->id);
        }
    }

        //  This is an alias function of oldPartner
    public function old_partner_id($value)
    {
        return $this->oldPartner($value);
    }
    
    public function newPartnerId($value)
    {
            $newPartner = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($newPartner) {
            return $this->builder->where('new_partner_id', '=', $newPartner->id);
        }
    }

        //  This is an alias function of newPartner
    public function new_partner_id($value)
    {
        return $this->newPartner($value);
    }
    
    public function iamUserId($value)
    {
            $iamUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($iamUser) {
            return $this->builder->where('iam_user_id', '=', $iamUser->id);
        }
    }

    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

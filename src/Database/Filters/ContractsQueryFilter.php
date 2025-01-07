<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
            

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class ContractsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function name($value)
    {
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }

        
    public function description($value)
    {
        return $this->builder->where('description', 'like', '%' . $value . '%');
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
     
    public function isSigned($value)
    {
        return $this->builder->where('is_signed', $value);
    }

        //  This is an alias function of isSigned
    public function is_signed($value)
    {
        return $this->isSigned($value);
    }
     
    public function termStartsStart($date)
    {
        return $this->builder->where('term_starts', '>=', $date);
    }

    public function termStartsEnd($date)
    {
        return $this->builder->where('term_starts', '<=', $date);
    }

    //  This is an alias function of termStarts
    public function term_starts_start($value)
    {
        return $this->termStartsStart($value);
    }

    //  This is an alias function of termStarts
    public function term_starts_end($value)
    {
        return $this->termStartsEnd($value);
    }

    public function termEndsStart($date)
    {
        return $this->builder->where('term_ends', '>=', $date);
    }

    public function termEndsEnd($date)
    {
        return $this->builder->where('term_ends', '<=', $date);
    }

    //  This is an alias function of termEnds
    public function term_ends_start($value)
    {
        return $this->termEndsStart($value);
    }

    //  This is an alias function of termEnds
    public function term_ends_end($value)
    {
        return $this->termEndsEnd($value);
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
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    public function iamUserId($value)
    {
            $iamUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if($iamUser) {
            return $this->builder->where('iam_user_id', '=', $iamUser->id);
        }
    }

    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE












}

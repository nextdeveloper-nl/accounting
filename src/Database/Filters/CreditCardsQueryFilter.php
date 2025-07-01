<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class CreditCardsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function name($value)
    {
        return $this->builder->where('name', 'ilike', '%' . $value . '%');
    }

        
    public function type($value)
    {
        return $this->builder->where('type', 'ilike', '%' . $value . '%');
    }

        
    public function ccHolderName($value)
    {
        return $this->builder->where('cc_holder_name', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of ccHolderName
    public function cc_holder_name($value)
    {
        return $this->ccHolderName($value);
    }
        
    public function ccNumber($value)
    {
        return $this->builder->where('cc_number', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of ccNumber
    public function cc_number($value)
    {
        return $this->ccNumber($value);
    }
        
    public function ccMonth($value)
    {
        return $this->builder->where('cc_month', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of ccMonth
    public function cc_month($value)
    {
        return $this->ccMonth($value);
    }
        
    public function ccYear($value)
    {
        return $this->builder->where('cc_year', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of ccYear
    public function cc_year($value)
    {
        return $this->ccYear($value);
    }
        
    public function ccCvv($value)
    {
        return $this->builder->where('cc_cvv', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of ccCvv
    public function cc_cvv($value)
    {
        return $this->ccCvv($value);
    }
    
    public function isDefault($value)
    {
        return $this->builder->where('is_default', $value);
    }

        //  This is an alias function of isDefault
    public function is_default($value)
    {
        return $this->isDefault($value);
    }
     
    public function isValid($value)
    {
        return $this->builder->where('is_valid', $value);
    }

        //  This is an alias function of isValid
    public function is_valid($value)
    {
        return $this->isValid($value);
    }
     
    public function isActive($value)
    {
        return $this->builder->where('is_active', $value);
    }

        //  This is an alias function of isActive
    public function is_active($value)
    {
        return $this->isActive($value);
    }
     
    public function is3dSecure($value)
    {
        return $this->builder->where('is_3d_secure', $value);
    }

        //  This is an alias function of is3dSecure
    public function is_3d_secure($value)
    {
        return $this->is3dSecure($value);
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

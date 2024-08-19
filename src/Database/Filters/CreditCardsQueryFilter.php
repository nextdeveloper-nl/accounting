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
        return $this->builder->where('name', 'like', '%' . $value . '%');
    }
    
    public function type($value)
    {
        return $this->builder->where('type', 'like', '%' . $value . '%');
    }
    
    public function ccHolderName($value)
    {
        return $this->builder->where('cc_holder_name', 'like', '%' . $value . '%');
    }
    
    public function ccNumber($value)
    {
        return $this->builder->where('cc_number', 'like', '%' . $value . '%');
    }
    
    public function ccMonth($value)
    {
        return $this->builder->where('cc_month', 'like', '%' . $value . '%');
    }
    
    public function ccYear($value)
    {
        return $this->builder->where('cc_year', 'like', '%' . $value . '%');
    }
    
    public function ccCvv($value)
    {
        return $this->builder->where('cc_cvv', 'like', '%' . $value . '%');
    }

    public function isDefault($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_default', $value);
    }

    public function isValid($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_valid', $value);
    }

    public function isActive($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_active', $value);
    }

    public function is3dSecure($value)
    {
        if(!is_bool($value)) {
            $value = false;
        }

        return $this->builder->where('is_3d_secure', $value);
    }

    public function createdAtStart($date)
    {
        return $this->builder->where('created_at', '>=', $date);
    }

    public function createdAtEnd($date)
    {
        return $this->builder->where('created_at', '<=', $date);
    }

    public function updatedAtStart($date)
    {
        return $this->builder->where('updated_at', '>=', $date);
    }

    public function updatedAtEnd($date)
    {
        return $this->builder->where('updated_at', '<=', $date);
    }

    public function deletedAtStart($date)
    {
        return $this->builder->where('deleted_at', '>=', $date);
    }

    public function deletedAtEnd($date)
    {
        return $this->builder->where('deleted_at', '<=', $date);
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

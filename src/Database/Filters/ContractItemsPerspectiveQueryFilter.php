<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class ContractItemsPerspectiveQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function objectType($value)
    {
        return $this->builder->where('object_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of objectType
    public function object_type($value)
    {
        return $this->objectType($value);
    }
        
    public function contractType($value)
    {
        return $this->builder->where('contract_type', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of contractType
    public function contract_type($value)
    {
        return $this->contractType($value);
    }
        
    public function accountName($value)
    {
        return $this->builder->where('account_name', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of accountName
    public function account_name($value)
    {
        return $this->accountName($value);
    }
        
    public function accountingIdentifier($value)
    {
        return $this->builder->where('accounting_identifier', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of accountingIdentifier
    public function accounting_identifier($value)
    {
        return $this->accountingIdentifier($value);
    }
    
    public function discount($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('discount', $operator, $value);
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
     
    public function isApproved($value)
    {
        return $this->builder->where('is_approved', $value);
    }

        //  This is an alias function of isApproved
    public function is_approved($value)
    {
        return $this->isApproved($value);
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

    public function accountingContractId($value)
    {
            $accountingContract = \NextDeveloper\Accounting\Database\Models\Contracts::where('uuid', $value)->first();

        if($accountingContract) {
            return $this->builder->where('accounting_contract_id', '=', $accountingContract->id);
        }
    }

        //  This is an alias function of accountingContract
    public function accounting_contract_id($value)
    {
        return $this->accountingContract($value);
    }
    
    public function commonCurrencyId($value)
    {
            $commonCurrency = \NextDeveloper\Commons\Database\Models\Currencies::where('uuid', $value)->first();

        if($commonCurrency) {
            return $this->builder->where('common_currency_id', '=', $commonCurrency->id);
        }
    }

        //  This is an alias function of commonCurrency
    public function common_currency_id($value)
    {
        return $this->commonCurrency($value);
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

    
    public function iamAccountTypeId($value)
    {
            $iamAccountType = \NextDeveloper\IAM\Database\Models\AccountTypes::where('uuid', $value)->first();

        if($iamAccountType) {
            return $this->builder->where('iam_account_type_id', '=', $iamAccountType->id);
        }
    }

        //  This is an alias function of iamAccountType
    public function iam_account_type_id($value)
    {
        return $this->iamAccountType($value);
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

    public function accountingContractId($value)
    {
        $contract = \NextDeveloper\Accounting\Database\Models\Contracts::where('uuid', $value)->first();

        if($contract) {
            return $this->builder->where('accounting_contract_id', $contract->id);
        }
    }















}

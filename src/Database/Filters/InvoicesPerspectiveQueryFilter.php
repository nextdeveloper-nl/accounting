<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                            

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class InvoicesPerspectiveQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function note($value)
    {
        return $this->builder->where('note', 'ilike', '%' . $value . '%');
    }

        
    public function name($value)
    {
        return $this->builder->where('name', 'ilike', '%' . $value . '%');
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
        
    public function commonCurrencyCode($value)
    {
        return $this->builder->where('common_currency_code', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of commonCurrencyCode
    public function common_currency_code($value)
    {
        return $this->commonCurrencyCode($value);
    }
    
    public function termYear($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('term_year', $operator, $value);
    }

        //  This is an alias function of termYear
    public function term_year($value)
    {
        return $this->termYear($value);
    }
    
    public function termMonth($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('term_month', $operator, $value);
    }

        //  This is an alias function of termMonth
    public function term_month($value)
    {
        return $this->termMonth($value);
    }
    
    public function isPaid($value)
    {
        return $this->builder->where('is_paid', $value);
    }

        //  This is an alias function of isPaid
    public function is_paid($value)
    {
        return $this->isPaid($value);
    }
     
    public function isPayable($value)
    {
        return $this->builder->where('is_payable', $value);
    }

        //  This is an alias function of isPayable
    public function is_payable($value)
    {
        return $this->isPayable($value);
    }
     
    public function isRefund($value)
    {
        return $this->builder->where('is_refund', $value);
    }

        //  This is an alias function of isRefund
    public function is_refund($value)
    {
        return $this->isRefund($value);
    }
     
    public function isSealed($value)
    {
        return $this->builder->where('is_sealed', $value);
    }

        //  This is an alias function of isSealed
    public function is_sealed($value)
    {
        return $this->isSealed($value);
    }
     
    public function isCommissionInvoice($value)
    {
        return $this->builder->where('is_commission_invoice', $value);
    }

        //  This is an alias function of isCommissionInvoice
    public function is_commission_invoice($value)
    {
        return $this->isCommissionInvoice($value);
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

    public function commonCountryId($value)
    {
            $commonCountry = \NextDeveloper\Commons\Database\Models\Countries::where('uuid', $value)->first();

        if($commonCountry) {
            return $this->builder->where('common_country_id', '=', $commonCountry->id);
        }
    }

        //  This is an alias function of commonCountry
    public function common_country_id($value)
    {
        return $this->commonCountry($value);
    }
    
    public function commonDomainId($value)
    {
            $commonDomain = \NextDeveloper\Commons\Database\Models\Domains::where('uuid', $value)->first();

        if($commonDomain) {
            return $this->builder->where('common_domain_id', '=', $commonDomain->id);
        }
    }

        //  This is an alias function of commonDomain
    public function common_domain_id($value)
    {
        return $this->commonDomain($value);
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

    public function crmAccountId($value)
    {
        $crmAccount = \NextDeveloper\CRM\Database\Models\Accounts::withoutGlobalScopes()->where('uuid', $value)->first();
        $accountingAccount = Accounts::withoutGlobalScopes()->where('iam_account_id', $crmAccount->iam_account_id)->first();

        return $this->builder->where('accounting_account_id', '=', $accountingAccount->id);
    }

    public function nonZeroInvoices()
    {
        return $this->builder->where('amount', '>', 0);
    }































}

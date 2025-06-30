<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class AccountsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function taxOffice($value)
    {
        return $this->builder->where('tax_office', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of taxOffice
    public function tax_office($value)
    {
        return $this->taxOffice($value);
    }
        
    public function taxNumber($value)
    {
        return $this->builder->where('tax_number', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of taxNumber
    public function tax_number($value)
    {
        return $this->taxNumber($value);
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
        
    public function tradeOfficeNumber($value)
    {
        return $this->builder->where('trade_office_number', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of tradeOfficeNumber
    public function trade_office_number($value)
    {
        return $this->tradeOfficeNumber($value);
    }
        
    public function tradeOffice($value)
    {
        return $this->builder->where('trade_office', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of tradeOffice
    public function trade_office($value)
    {
        return $this->tradeOffice($value);
    }
        
    public function trMersis($value)
    {
        return $this->builder->where('tr_mersis', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of trMersis
    public function tr_mersis($value)
    {
        return $this->trMersis($value);
    }
    
    public function isSuspended($value)
    {
        return $this->builder->where('is_suspended', $value);
    }

        //  This is an alias function of isSuspended
    public function is_suspended($value)
    {
        return $this->isSuspended($value);
    }
     
    public function isDisabled($value)
    {
        return $this->builder->where('is_disabled', $value);
    }

        //  This is an alias function of isDisabled
    public function is_disabled($value)
    {
        return $this->isDisabled($value);
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
    
    public function distributorId($value)
    {
            $distributor = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($distributor) {
            return $this->builder->where('distributor_id', '=', $distributor->id);
        }
    }

        //  This is an alias function of distributor
    public function distributor_id($value)
    {
        return $this->distributor($value);
    }
    
    public function salesPartnerId($value)
    {
            $salesPartner = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($salesPartner) {
            return $this->builder->where('sales_partner_id', '=', $salesPartner->id);
        }
    }

        //  This is an alias function of salesPartner
    public function sales_partner_id($value)
    {
        return $this->salesPartner($value);
    }
    
    public function integratorPartnerId($value)
    {
            $integratorPartner = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($integratorPartner) {
            return $this->builder->where('integrator_partner_id', '=', $integratorPartner->id);
        }
    }

        //  This is an alias function of integratorPartner
    public function integrator_partner_id($value)
    {
        return $this->integratorPartner($value);
    }
    
    public function affiliatePartnerId($value)
    {
            $affiliatePartner = \NextDeveloper\Accounting\Database\Models\Accounts::where('uuid', $value)->first();

        if($affiliatePartner) {
            return $this->builder->where('affiliate_partner_id', '=', $affiliatePartner->id);
        }
    }

        //  This is an alias function of affiliatePartner
    public function affiliate_partner_id($value)
    {
        return $this->affiliatePartner($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE





























































}

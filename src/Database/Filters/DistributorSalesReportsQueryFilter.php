<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class DistributorSalesReportsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function currencyCode($value)
    {
        return $this->builder->where('currency_code', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of currencyCode
    public function currency_code($value)
    {
        return $this->currencyCode($value);
    }
    
    public function invoiceCount($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('invoice_count', $operator, $value);
    }

        //  This is an alias function of invoiceCount
    public function invoice_count($value)
    {
        return $this->invoiceCount($value);
    }
    
    public function unpaidInvoiceCount($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('unpaid_invoice_count', $operator, $value);
    }

        //  This is an alias function of unpaidInvoiceCount
    public function unpaid_invoice_count($value)
    {
        return $this->unpaidInvoiceCount($value);
    }
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }

    
    public function distributorId($value)
    {
            $distributor = \NextDeveloper\\Database\Models\Distributors::where('uuid', $value)->first();

        if($distributor) {
            return $this->builder->where('distributor_id', '=', $distributor->id);
        }
    }

        //  This is an alias function of distributor
    public function distributor_id($value)
    {
        return $this->distributor($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}

<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class MonthlyPaidInvoicesPerformanceQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function monthName($value)
    {
        return $this->builder->where('month_name', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of monthName
    public function month_name($value)
    {
        return $this->monthName($value);
    }
        
    public function monthCode($value)
    {
        return $this->builder->where('month_code', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of monthCode
    public function month_code($value)
    {
        return $this->monthCode($value);
    }
    
    public function count($value)
    {
        $operator = substr($value, 0, 1);

        if ($operator != '<' || $operator != '>') {
            $operator = '=';
        } else {
            $value = substr($value, 1);
        }

        return $this->builder->where('count', $operator, $value);
    }

    
    public function monthStartStart($date)
    {
        return $this->builder->where('month_start', '>=', $date);
    }

    public function monthStartEnd($date)
    {
        return $this->builder->where('month_start', '<=', $date);
    }

    //  This is an alias function of monthStart
    public function month_start_start($value)
    {
        return $this->monthStartStart($value);
    }

    //  This is an alias function of monthStart
    public function month_start_end($value)
    {
        return $this->monthStartEnd($value);
    }

    public function monthEndStart($date)
    {
        return $this->builder->where('month_end', '>=', $date);
    }

    public function monthEndEnd($date)
    {
        return $this->builder->where('month_end', '<=', $date);
    }

    //  This is an alias function of monthEnd
    public function month_end_start($value)
    {
        return $this->monthEndStart($value);
    }

    //  This is an alias function of monthEnd
    public function month_end_end($value)
    {
        return $this->monthEndEnd($value);
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
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE











}

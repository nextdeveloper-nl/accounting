<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;


/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class WeeklyPaidInvoicesPerformanceQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function weekNumber($value)
    {
        return $this->builder->where('week_number', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of weekNumber
    public function week_number($value)
    {
        return $this->weekNumber($value);
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

    
    public function weekStartStart($date)
    {
        return $this->builder->where('week_start', '>=', $date);
    }

    public function weekStartEnd($date)
    {
        return $this->builder->where('week_start', '<=', $date);
    }

    //  This is an alias function of weekStart
    public function week_start_start($value)
    {
        return $this->weekStartStart($value);
    }

    //  This is an alias function of weekStart
    public function week_start_end($value)
    {
        return $this->weekStartEnd($value);
    }

    public function weekEndStart($date)
    {
        return $this->builder->where('week_end', '>=', $date);
    }

    public function weekEndEnd($date)
    {
        return $this->builder->where('week_end', '<=', $date);
    }

    //  This is an alias function of weekEnd
    public function week_end_start($value)
    {
        return $this->weekEndStart($value);
    }

    //  This is an alias function of weekEnd
    public function week_end_end($value)
    {
        return $this->weekEndEnd($value);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE











}

<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
        

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class PaymentCheckoutSessionsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function isInvalidated($value)
    {
        return $this->builder->where('is_invalidated', $value);
    }

        //  This is an alias function of isInvalidated
    public function is_invalidated($value)
    {
        return $this->isInvalidated($value);
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

    public function accountingPaymentGatewayId($value)
    {
            $accountingPaymentGateway = \NextDeveloper\Accounting\Database\Models\PaymentGateways::where('uuid', $value)->first();

        if($accountingPaymentGateway) {
            return $this->builder->where('accounting_payment_gateway_id', '=', $accountingPaymentGateway->id);
        }
    }

        //  This is an alias function of accountingPaymentGateway
    public function accounting_payment_gateway_id($value)
    {
        return $this->accountingPaymentGateway($value);
    }
    
    public function accountingInvoiceId($value)
    {
            $accountingInvoice = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($accountingInvoice) {
            return $this->builder->where('accounting_invoice_id', '=', $accountingInvoice->id);
        }
    }

        //  This is an alias function of accountingInvoice
    public function accounting_invoice_id($value)
    {
        return $this->accountingInvoice($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}

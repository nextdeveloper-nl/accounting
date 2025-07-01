<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                    

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class TransactionsQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function gatewayResponse($value)
    {
        return $this->builder->where('gateway_response', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of gatewayResponse
    public function gateway_response($value)
    {
        return $this->gatewayResponse($value);
    }
        
    public function conversationIdentifier($value)
    {
        return $this->builder->where('conversation_identifier', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of conversationIdentifier
    public function conversation_identifier($value)
    {
        return $this->conversationIdentifier($value);
    }
    
    public function isPending($value)
    {
        return $this->builder->where('is_pending', $value);
    }

        //  This is an alias function of isPending
    public function is_pending($value)
    {
        return $this->isPending($value);
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
    
    public function iamAccountId($value)
    {
            $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::where('uuid', $value)->first();

        if($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
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























































}

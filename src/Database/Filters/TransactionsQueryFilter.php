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
        return $this->builder->where('gateway_response', 'like', '%' . $value . '%');
    }
    
    public function conversationId($value)
    {
        return $this->builder->where('conversation_id', 'like', '%' . $value . '%');
    }

    public function isPending()
    {
        return $this->builder->where('is_pending', true);
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

    public function accountingInvoiceId($value)
    {
            $accountingInvoice = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($accountingInvoice) {
            return $this->builder->where('accounting_invoice_id', '=', $accountingInvoice->id);
        }
    }

    public function commonCurrencyId($value)
    {
            $commonCurrency = \NextDeveloper\Commons\Database\Models\Currencies::where('uuid', $value)->first();

        if($commonCurrency) {
            return $this->builder->where('common_currency_id', '=', $commonCurrency->id);
        }
    }

    public function accountingPaymentGatewayId($value)
    {
            $accountingPaymentGateway = \NextDeveloper\Accounting\Database\Models\PaymentGateways::where('uuid', $value)->first();

        if($accountingPaymentGateway) {
            return $this->builder->where('accounting_payment_gateway_id', '=', $accountingPaymentGateway->id);
        }
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

    public function conversationId($value)
    {
            $conversation = \NextDeveloper\\Database\Models\Conversations::where('uuid', $value)->first();

        if($conversation) {
            return $this->builder->where('conversation_id', '=', $conversation->id);
        }
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

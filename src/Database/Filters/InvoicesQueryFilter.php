<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;
                                

/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class InvoicesQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;
    
    public function invoiceNumber($value)
    {
        return $this->builder->where('invoice_number', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of invoiceNumber
    public function invoice_number($value)
    {
        return $this->invoiceNumber($value);
    }
        
    public function note($value)
    {
        return $this->builder->where('note', 'ilike', '%' . $value . '%');
    }

        
    public function cancellationReason($value)
    {
        return $this->builder->where('cancellation_reason', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of cancellationReason
    public function cancellation_reason($value)
    {
        return $this->cancellationReason($value);
    }
        
    public function paymentLinkUrl($value)
    {
        return $this->builder->where('payment_link_url', 'ilike', '%' . $value . '%');
    }

        //  This is an alias function of paymentLinkUrl
    public function payment_link_url($value)
    {
        return $this->paymentLinkUrl($value);
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
     
    public function isRefund($value)
    {
        return $this->builder->where('is_refund', $value);
    }

        //  This is an alias function of isRefund
    public function is_refund($value)
    {
        return $this->isRefund($value);
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
     
    public function isSealed($value)
    {
        return $this->builder->where('is_sealed', $value);
    }

        //  This is an alias function of isSealed
    public function is_sealed($value)
    {
        return $this->isSealed($value);
    }
     
    public function isCancelled($value)
    {
        return $this->builder->where('is_cancelled', $value);
    }

        //  This is an alias function of isCancelled
    public function is_cancelled($value)
    {
        return $this->isCancelled($value);
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
     
    public function dueDateStart($date)
    {
        return $this->builder->where('due_date', '>=', $date);
    }

    public function dueDateEnd($date)
    {
        return $this->builder->where('due_date', '<=', $date);
    }

    //  This is an alias function of dueDate
    public function due_date_start($value)
    {
        return $this->dueDateStart($value);
    }

    //  This is an alias function of dueDate
    public function due_date_end($value)
    {
        return $this->dueDateEnd($value);
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

    
    public function distributorCommissionInvoiceId($value)
    {
            $distributorCommissionInvoice = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($distributorCommissionInvoice) {
            return $this->builder->where('distributor_commission_invoice_id', '=', $distributorCommissionInvoice->id);
        }
    }

        //  This is an alias function of distributorCommissionInvoice
    public function distributor_commission_invoice_id($value)
    {
        return $this->distributorCommissionInvoice($value);
    }
    
    public function integratorCommissionInvoiceId($value)
    {
            $integratorCommissionInvoice = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($integratorCommissionInvoice) {
            return $this->builder->where('integrator_commission_invoice_id', '=', $integratorCommissionInvoice->id);
        }
    }

        //  This is an alias function of integratorCommissionInvoice
    public function integrator_commission_invoice_id($value)
    {
        return $this->integratorCommissionInvoice($value);
    }
    
    public function resellerCommissionInvoiceId($value)
    {
            $resellerCommissionInvoice = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($resellerCommissionInvoice) {
            return $this->builder->where('reseller_commission_invoice_id', '=', $resellerCommissionInvoice->id);
        }
    }

        //  This is an alias function of resellerCommissionInvoice
    public function reseller_commission_invoice_id($value)
    {
        return $this->resellerCommissionInvoice($value);
    }
    
    public function affiliateCommissionInvoiceId($value)
    {
            $affiliateCommissionInvoice = \NextDeveloper\Accounting\Database\Models\Invoices::where('uuid', $value)->first();

        if($affiliateCommissionInvoice) {
            return $this->builder->where('affiliate_commission_invoice_id', '=', $affiliateCommissionInvoice->id);
        }
    }

        //  This is an alias function of affiliateCommissionInvoice
    public function affiliate_commission_invoice_id($value)
    {
        return $this->affiliateCommissionInvoice($value);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
















































































}

<?php

namespace NextDeveloper\Accounting\Database\Filters;

use Illuminate\Database\Eloquent\Builder;
use NextDeveloper\Commons\Database\Filters\AbstractQueryFilter;


/**
 * This class automatically puts where clause on database so that use can filter
 * data returned from the query.
 */
class AccountsPerspectiveQueryFilter extends AbstractQueryFilter
{

    /**
     * @var Builder
     */
    protected $builder;

    public function name($value)
    {
        return $this->builder->where('name', 'ilike', '%' . $value . '%');
    }


    public function phoneNumber($value)
    {
        return $this->builder->where('phone_number', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of phoneNumber
    public function phone_number($value)
    {
        return $this->phoneNumber($value);
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

    public function taxOffice($value)
    {
        return $this->builder->where('tax_office', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of taxOffice
    public function tax_office($value)
    {
        return $this->taxOffice($value);
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

    public function trMersis($value)
    {
        return $this->builder->where('tr_mersis', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of trMersis
    public function tr_mersis($value)
    {
        return $this->trMersis($value);
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

    public function tradeOfficeNumber($value)
    {
        return $this->builder->where('trade_office_number', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of tradeOfficeNumber
    public function trade_office_number($value)
    {
        return $this->tradeOfficeNumber($value);
    }

    public function distributorPartner($value)
    {
        return $this->builder->where('distributor_partner', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of distributorPartner
    public function distributor_partner($value)
    {
        return $this->distributorPartner($value);
    }

    public function integratorPartner($value)
    {
        return $this->builder->where('integrator_partner', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of integratorPartner
    public function integrator_partner($value)
    {
        return $this->integratorPartner($value);
    }

    public function salesPartner($value)
    {
        return $this->builder->where('sales_partner', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of salesPartner
    public function sales_partner($value)
    {
        return $this->salesPartner($value);
    }

    public function affiliatePartner($value)
    {
        return $this->builder->where('affiliate_partner', 'ilike', '%' . $value . '%');
    }

    //  This is an alias function of affiliatePartner
    public function affiliate_partner($value)
    {
        return $this->affiliatePartner($value);
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

        if ($commonCountry) {
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

        if ($commonDomain) {
            return $this->builder->where('common_domain_id', '=', $commonDomain->id);
        }
    }

    //  This is an alias function of commonDomain
    public function common_domain_id($value)
    {
        return $this->commonDomain($value);
    }

    public function iamUserId($value)
    {
        $iamUser = \NextDeveloper\IAM\Database\Models\Users::where('uuid', $value)->first();

        if ($iamUser) {
            return $this->builder->where('iam_user_id', '=', $iamUser->id);
        }
    }


    public function iamAccountTypeId($value)
    {
        $iamAccountType = \NextDeveloper\IAM\Database\Models\AccountTypes::where('uuid', $value)->first();

        if ($iamAccountType) {
            return $this->builder->where('iam_account_type_id', '=', $iamAccountType->id);
        }
    }

    //  This is an alias function of iamAccountType
    public function iam_account_type_id($value)
    {
        return $this->iamAccountType($value);
    }

    public function iamAccountId($value)
    {
        $iamAccount = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScopes()->where('uuid', $value)->first();

        if ($iamAccount) {
            return $this->builder->where('iam_account_id', '=', $iamAccount->id);
        }
    }


    public function commonCurrencyId($value)
    {
        $commonCurrency = \NextDeveloper\Commons\Database\Models\Currencies::where('uuid', $value)->first();

        if ($commonCurrency) {
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
        $distributor = \NextDeveloper\Accounting\Database\Models\Distributors::where('uuid', $value)->first();

        if ($distributor) {
            return $this->builder->where('distributor_id', '=', $distributor->id);
        }
    }

    //  This is an alias function of distributor
    public function distributor_id($value)
    {
        return $this->distributor($value);
    }

    public function integratorPartnerId($value)
    {
        $integratorPartner = \NextDeveloper\Accounting\Database\Models\IntegratorPartners::where('uuid', $value)->first();

        if ($integratorPartner) {
            return $this->builder->where('integrator_partner_id', '=', $integratorPartner->id);
        }
    }

    //  This is an alias function of integratorPartner
    public function integrator_partner_id($value)
    {
        return $this->integratorPartner($value);
    }

    public function salesPartnerId($value)
    {
        $salesPartner = \NextDeveloper\Accounting\Database\Models\SalesPartners::where('uuid', $value)->first();

        if ($salesPartner) {
            return $this->builder->where('sales_partner_id', '=', $salesPartner->id);
        }
    }

    //  This is an alias function of salesPartner
    public function sales_partner_id($value)
    {
        return $this->salesPartner($value);
    }

    public function affiliatePartnerId($value)
    {
        $affiliatePartner = \NextDeveloper\Accounting\Database\Models\AffiliatePartners::where('uuid', $value)->first();

        if ($affiliatePartner) {
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

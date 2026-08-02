-- PostgreSQL
-- VIEW (reconstructed from database.leo.v4 reference SQL, already consistent with the
-- current NextDeveloper\Accounting\Database\Models\AccountsPerspective fillable list.)

create or replace view accounting_accounts_perspective as
select
    aa.id,
    aa.uuid,
    ia.name,
    ia.phone_number,
    ia.common_country_id,
    ia.common_domain_id,
    ia.iam_user_id,
    ia.iam_account_type_id,
    aa.iam_account_id,
    aa.tax_number,
    aa.tax_office,
    aa.accounting_identifier,
    aa.credit,
    aa.common_currency_id,
    (select code from common_currencies n_cc where n_cc.id = aa.common_currency_id) as common_currency_code,
    aa.tr_mersis,
    aa.trade_office,
    aa.trade_office_number,

    aa.distributor_id,
    aa.integrator_partner_id,
    aa.sales_partner_id,
    aa.affiliate_partner_id,

    aa.is_distributor,
    aa.is_integrator,
    aa.is_reseller,
    aa.is_affiliate,
    aa.is_vendor,

    (
        select name from iam_accounts n_ia where n_ia.id = (select n_aa.iam_account_id from accounting_accounts n_aa where n_aa.id = aa.distributor_id )
    ) as distributor_partner,
    (
        select name from iam_accounts n_ia where n_ia.id = (select n_aa.iam_account_id from accounting_accounts n_aa where n_aa.id = aa.integrator_partner_id )
    ) as integrator_partner,
    (
        select name from iam_accounts n_ia where n_ia.id = (select n_aa.iam_account_id from accounting_accounts n_aa where n_aa.id = aa.sales_partner_id )
    ) as sales_partner,
    (
        select name from iam_accounts n_ia where n_ia.id = (select n_aa.iam_account_id from accounting_accounts n_aa where n_aa.id = aa.affiliate_partner_id )
    ) as affiliate_partner,

    aa.created_at,
    aa.updated_at,
    aa.deleted_at
from accounting_accounts aa
inner join iam_accounts ia on aa.iam_account_id = ia.id;

comment on column accounting_accounts_perspective.distributor_id is '[alias:accounting_account_id]';
comment on column accounting_accounts_perspective.integrator_partner_id is '[alias:accounting_account_id]';
comment on column accounting_accounts_perspective.sales_partner_id is '[alias:accounting_account_id]';
comment on column accounting_accounts_perspective.affiliate_partner_id is '[alias:accounting_account_id]';

-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW accounting_vendors_perspective AS
SELECT aa.id,
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
    ( SELECT n_cc.code
           FROM common_currencies n_cc
          WHERE n_cc.id = aa.common_currency_id) AS common_currency_code,
    aa.tr_mersis,
    aa.trade_office,
    aa.trade_office_number,
    aa.created_at,
    aa.updated_at,
    aa.deleted_at
   FROM accounting_accounts aa
     JOIN iam_accounts ia ON aa.iam_account_id = ia.id
  WHERE aa.is_vendor = true;

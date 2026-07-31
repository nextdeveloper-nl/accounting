-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW accounting_invoices_perspective AS
SELECT ai.id,
    ai.uuid,
    ai.term_year,
    ai.term_month,
    ai.amount,
    ai.is_paid,
    ai.is_payable,
    ai.is_refund,
    ai.is_sealed,
    ai.is_commission_invoice,
    ai.note,
    ia.name,
    ia.common_country_id,
    ia.common_domain_id,
    ia.id AS iam_account_id,
    ia.iam_user_id,
    ia.iam_account_type_id,
    aa.accounting_identifier,
    aa.credit,
    ai.payment_link_url,
    aa.common_currency_id,
    ( SELECT n_cc.code
           FROM common_currencies n_cc
          WHERE n_cc.id = ai.common_currency_id) AS common_currency_code,
    aa.id AS accounting_account_id,
    ai.created_at,
    ai.updated_at,
    ai.deleted_at
   FROM accounting_invoices ai
     JOIN accounting_accounts aa ON ai.accounting_account_id = aa.id
     JOIN iam_accounts ia ON aa.iam_account_id = ia.id
  WHERE ai.amount > 0::numeric AND aa.is_suspended = false AND aa.is_disabled = false AND ai.is_cancelled = false;

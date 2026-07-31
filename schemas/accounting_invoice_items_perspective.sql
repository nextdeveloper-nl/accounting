-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW accounting_invoice_items_perspective AS
SELECT aii.id,
    aii.uuid,
    ai.id AS accounting_invoice_id,
    ai.term_year,
    ai.term_month,
    ai.amount AS invoice_amount,
    aii.object_type,
    aii.object_id,
    aii.unit_price,
    aii.quantity,
    aii.total_price,
    ia.id AS iam_account_id,
    ia.name,
    ia.iam_user_id,
    aa.accounting_identifier,
    aa.credit,
    aa.common_currency_id,
    ( SELECT n_cc.code
           FROM common_currencies n_cc
          WHERE n_cc.id = aii.common_currency_id) AS common_currency_code,
    ai.created_at,
    ai.updated_at,
    ai.deleted_at
   FROM accounting_invoice_items aii
     JOIN accounting_invoices ai ON aii.accounting_invoice_id = ai.id
     JOIN accounting_accounts aa ON ai.accounting_account_id = aa.id
     JOIN iam_accounts ia ON aa.iam_account_id = ia.id;

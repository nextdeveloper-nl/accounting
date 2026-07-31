-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW accounting_contracts_perspective AS
SELECT ac.id,
    ac.uuid,
    ac.name,
    ac.description,
    ac.term_starts,
    ac.term_ends,
    ac.is_signed,
    ac.is_approved,
    ( SELECT count(*) AS count
           FROM accounting_contract_items
          WHERE accounting_contract_items.accounting_contract_id = ac.id) AS contract_item_count,
    ia.name AS account_name,
    ac.iam_account_id,
    ac.iam_user_id,
    ia.iam_account_type_id,
    aa.accounting_identifier,
    aa.credit,
    aa.common_currency_id,
    aa.id AS accounting_account_id,
    ac.created_at,
    ac.updated_at,
    ac.deleted_at
   FROM accounting_contracts ac
     JOIN accounting_accounts aa ON ac.accounting_account_id = aa.id
     JOIN iam_accounts ia ON aa.iam_account_id = ia.id;

-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW accounting_contract_items_perspective AS
SELECT aci.id,
    aci.uuid,
    aci.object_type,
    aci.object_id,
    aci.accounting_contract_id,
    ac.term_starts,
    ac.term_ends,
    aci.price,
    aci.discount,
    aci.common_currency_id,
    aci.contract_type,
    ac.is_signed,
    ac.is_approved,
    ia.name AS account_name,
    ia.id AS iam_account_id,
    ia.iam_user_id,
    ia.iam_account_type_id,
    aa.accounting_identifier,
    aa.credit,
    aa.id AS accounting_account_id,
    ac.created_at,
    ac.updated_at,
    ac.deleted_at
   FROM accounting_contract_items aci
     JOIN accounting_contracts ac ON aci.accounting_contract_id = ac.id
     JOIN accounting_accounts aa ON ac.accounting_account_id = aa.id
     JOIN iam_accounts ia ON aa.iam_account_id = ia.id
  WHERE aci.deleted_at IS NULL;

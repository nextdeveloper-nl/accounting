-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)

CREATE OR REPLACE VIEW accounting_partnerships_perspective AS
SELECT ap.id,
    ap.uuid,
    ia.name,
    ap.partner_code,
    ap.is_brand_ambassador,
    ap.customer_count,
    ap.level,
    ap.reward_points,
    ap.boosts,
    ap.mystery_box,
    ap.badges,
    ap.is_approved,
    ap.technical_capabilities,
    ap.industry,
    ap.sector_focus,
    ap.special_interest,
    ap.compliance_certifications,
    ap.target_group,
    ap.meeting_link,
    ap.iam_account_id,
    ap.created_at,
    ap.updated_at,
    ap.deleted_at
   FROM accounting_partnerships ap
     JOIN iam_accounts ia ON ap.iam_account_id = ia.id
     JOIN accounting_accounts aa ON ia.id = aa.iam_account_id;

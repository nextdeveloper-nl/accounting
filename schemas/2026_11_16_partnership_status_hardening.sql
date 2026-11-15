-- PostgreSQL
-- Follow-up to 2026_11_15_multi_tenant_partnership_combined.sql (security review findings).
-- Run manually, statement by statement (autocommit) on every DB where the combined file was already applied.
--
-- 1. The backfill marked EVERY existing partnership as 'active', including unapproved applicants.
--    Correct rule: active when approved OR the account holds a partner role flag (that is what drives
--    commissions/assignments today); otherwise pending. Only touches rows the backfill created
--    (tenant set, no accepted_at, i.e. never went through the new invite/accept flow).
update accounting_partnerships p
set status = 'pending'
where p.status = 'active'
  and p.accepted_at is null
  and p.invited_at is null
  and coalesce(p.is_approved, false) = false
  and not exists (select 1
                  from accounting_accounts a
                  where a.id = p.accounting_account_id
                    and (a.is_distributor or a.is_integrator or a.is_reseller or a.is_affiliate));

-- 2. Role assignments must only exist for non-pending partnerships.
update accounting_partnership_role_assignments ra
set finished_at = now(), status = 'inactive'
from accounting_partnerships p
where p.id = ra.partnership_id
  and p.status = 'pending'
  and ra.finished_at is null;

-- 3. Fail closed: new rows are pending unless the service explicitly activates them.
alter table accounting_partnerships alter column status set default 'pending';

-- 4. Restrict allowed values (NOT VALID first, then validate, to keep the lock short).
alter table accounting_partnerships
    add constraint chk_accounting_partnerships_status
        check (status in ('pending', 'active', 'rejected', 'revoked')) not valid;
alter table accounting_partnerships validate constraint chk_accounting_partnerships_status;

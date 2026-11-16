-- PostgreSQL
-- OPTIONAL. Carries the real start date of a customer <-> partner relationship over to accounting_account_partners.
-- Run manually, only on a database where accounting_partner_assignments HAS rows (check PART 1 first).
--
-- Why: the backfill created every accounting_account_partners row with started_at = the moment of the backfill.
-- The generic commission engine only pays an invoice that was created while the link was open (started_at <= invoice
-- date), exactly like the old jobs did with accounting_partner_assignments.started_at. A link that starts "today"
-- would therefore block paid invoices that the old jobs would still have commissioned.
--
-- Not needed on dev: accounting_partner_assignments is empty there (the old jobs create those rows lazily on their
-- first run, with started_at = now), so there is no earlier date to carry over.

-- =============================================================================================
-- PART 1: DIAGNOSTICS (read only)
-- =============================================================================================

-- D1. Rows in the legacy assignment log (empty = nothing to carry over, stop here).
select type, count(*) as total, count(*) filter (where finished_at is null) as open_rows,
       min(started_at)::date as earliest, max(started_at)::date as latest
from accounting_partner_assignments
where deleted_at is null
group by type
order by type;

-- D2. Links that have a matching open legacy assignment (same customer, same role, same partner) and would change.
select r.slug, count(*) as links_to_update
from accounting_account_partners ap
         join accounting_partnership_roles r on r.id = ap.partnership_role_id
         join accounting_partnerships p on p.id = ap.partnership_id
where ap.deleted_at is null
  and ap.finished_at is null
  and exists (select 1
              from accounting_partner_assignments pa
              where pa.deleted_at is null
                and pa.finished_at is null
                and pa.accounting_account_id = ap.accounting_account_id
                and pa.type = r.slug
                and pa.new_partner_id = p.accounting_account_id
                and pa.started_at < ap.started_at)
group by r.slug
order by r.slug;

-- =============================================================================================
-- PART 2: FIX (one statement = one transaction, idempotent: only moves started_at EARLIER, never later)
-- =============================================================================================
update accounting_account_partners ap
set started_at = legacy.started_at
from (select ap2.id as link_id, min(pa.started_at) as started_at
      from accounting_account_partners ap2
               join accounting_partnership_roles r on r.id = ap2.partnership_role_id
               join accounting_partnerships p on p.id = ap2.partnership_id
               join accounting_partner_assignments pa
                    on pa.accounting_account_id = ap2.accounting_account_id
                        and pa.type = r.slug
                        and pa.new_partner_id = p.accounting_account_id
                        and pa.finished_at is null
                        and pa.deleted_at is null
      where ap2.deleted_at is null
        and ap2.finished_at is null
      group by ap2.id) legacy
where ap.id = legacy.link_id
  and legacy.started_at < ap.started_at;

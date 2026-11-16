-- PostgreSQL
-- Data check and fix after the multi-tenant partnership backfill (2026_11_15_multi_tenant_partnership_combined.sql).
-- Run manually. NOT applied automatically (project rule: no direct SQL from tooling).
--
-- HOW TO USE
--   1. Run PART 1 (read only diagnostics) and look at every result. Each query returns ZERO rows when the data is fine.
--   2. Only if something shows up, run PART 2 (the fix) as ONE statement (a single DO block = one transaction).
--   3. Run PART 1 again: everything should be empty.
--
-- Assumes the PlusClouds tenant is iam_accounts.id = 5. Change it in PART 2 (v_tenant) and in the diagnostics (:5).
-- Run 2026_11_16_partnership_status_hardening.sql BEFORE this file.

-- =============================================================================================
-- PART 1: DIAGNOSTICS (read only)
-- =============================================================================================

-- D1. Partnerships that have no tenant yet (the backfill sets it for all of them).
select id, uuid, accounting_account_id, status
from accounting_partnerships
where tenant_account_id is null
  and deleted_at is null;

-- D2. Accounts that carry a partner flag but have NO partnership row: the backfill only created role assignments for
--     existing partnership rows, so these partners are invisible to the new model and earn no commission through it.
select a.id, a.uuid, a.iam_account_id, a.is_distributor, a.is_integrator, a.is_reseller, a.is_affiliate
from accounting_accounts a
where a.deleted_at is null
  and (a.is_distributor or a.is_integrator or a.is_reseller or a.is_affiliate)
  and not exists (select 1
                  from accounting_partnerships p
                  where p.accounting_account_id = a.id
                    and p.deleted_at is null);

-- D3. Customers whose old pointer columns reference a partner account that has no (non deleted) partnership row, so
--     no accounting_account_partners link could be created for it.
select c.id as customer_id, v.slug as role_slug, v.partner_account_id
from accounting_accounts c
         cross join lateral (values ('distributor', c.distributor_id),
                                    ('reseller', c.sales_partner_id),
                                    ('integrator', c.integrator_partner_id),
                                    ('affiliate', c.affiliate_partner_id)) as v(slug, partner_account_id)
where c.deleted_at is null
  and v.partner_account_id is not null
  and not exists (select 1
                  from accounting_partnerships p
                  where p.accounting_account_id = v.partner_account_id
                    and p.deleted_at is null);

-- D4. Customers without a tenant.
select id, uuid, iam_account_id
from accounting_accounts
where tenant_account_id is null
  and deleted_at is null;

-- D5a. ACTIVE partnerships that hold no active role assignment (they exist but can earn nothing).
select p.id, p.uuid, p.accounting_account_id
from accounting_partnerships p
where p.status = 'active'
  and p.deleted_at is null
  and not exists (select 1
                  from accounting_partnership_role_assignments ra
                  where ra.partnership_id = p.id
                    and ra.deleted_at is null
                    and ra.finished_at is null
                    and ra.status = 'active');

-- D5b. Role assignments or customer links that are still open on a partnership that is NOT active.
select 'role_assignment' as kind, ra.id, ra.partnership_id, p.status
from accounting_partnership_role_assignments ra
         join accounting_partnerships p on p.id = ra.partnership_id
where ra.deleted_at is null and ra.finished_at is null and ra.status = 'active' and p.status <> 'active'
union all
select 'account_partner', ap.id, ap.partnership_id, p.status
from accounting_account_partners ap
         join accounting_partnerships p on p.id = ap.partnership_id
where ap.deleted_at is null and ap.finished_at is null and p.status <> 'active';

-- D6. Duplicate partner codes (the unique index should make this impossible; if rows show up the index is missing).
select partner_code, count(*)
from accounting_partnerships
where deleted_at is null and partner_code is not null
group by partner_code
having count(*) > 1;

-- D7. Tenant mismatches between linked rows (a role / partner / customer from another tenant).
select 'role_assignment' as kind, ra.id
from accounting_partnership_role_assignments ra
         join accounting_partnerships p on p.id = ra.partnership_id
         join accounting_partnership_roles r on r.id = ra.partnership_role_id
where ra.deleted_at is null
  and (ra.tenant_account_id <> p.tenant_account_id or ra.tenant_account_id <> r.tenant_account_id)
union all
select 'account_partner', ap.id
from accounting_account_partners ap
         join accounting_partnerships p on p.id = ap.partnership_id
         join accounting_partnership_roles r on r.id = ap.partnership_role_id
         join accounting_accounts c on c.id = ap.accounting_account_id
where ap.deleted_at is null
  and (ap.tenant_account_id <> p.tenant_account_id
    or ap.tenant_account_id <> r.tenant_account_id
    or ap.tenant_account_id <> coalesce(c.tenant_account_id, ap.tenant_account_id));

-- D8. Tenants that run a program but have no partnership role defined (they cannot invite anyone).
select pp.tenant_account_id
from accounting_partner_programs pp
where pp.deleted_at is null
  and pp.status = 'active'
  and not exists (select 1
                  from accounting_partnership_roles r
                  where r.tenant_account_id = pp.tenant_account_id
                    and r.deleted_at is null);

-- =============================================================================================
-- PART 2: FIX (one DO block = one transaction, idempotent). Review PART 1 first.
-- =============================================================================================
do
$datafix$
    declare
        v_tenant constant bigint := 5; -- iam_accounts.id of the PlusClouds tenant
        v_count bigint;
        v_account record;
    begin
        if not exists (select 1 from iam_accounts where id = v_tenant) then
            raise exception 'iam_accounts.id % does not exist, fix v_tenant', v_tenant;
        end if;

        -- F1. Partnership rows for partner flagged accounts that have none (D2). They were flagged by staff, so they
        --     are approved and active. Only the columns the application itself sets on create are filled in
        --     (see PartnershipsService::create), everything else uses the table defaults. The partner code is a
        --     random 10 character string, re-drawn until it is unused (codes are globally unique).
        for v_account in
            select a.id, a.iam_account_id
            from accounting_accounts a
            where a.deleted_at is null
              and (a.is_distributor or a.is_integrator or a.is_reseller or a.is_affiliate)
              and not exists (select 1
                              from accounting_partnerships p
                              where p.accounting_account_id = a.id and p.deleted_at is null)
            loop
                insert into accounting_partnerships
                (iam_account_id, accounting_account_id, tenant_account_id, partner_code, customer_count, level,
                 is_approved, status, accepted_at)
                select v_account.iam_account_id,
                       v_account.id,
                       v_tenant,
                       code,
                       0,
                       1,
                       true,
                       'active',
                       now()
                from (select substr(md5(random()::text || clock_timestamp()::text || v_account.id::text), 1, 10) as code) c
                where not exists (select 1 from accounting_partnerships x where x.partner_code = c.code);
            end loop;

        -- Materialised hierarchy path for rows that have none (root partners: just their own id).
        update accounting_partnerships
        set path = id::text
        where tenant_account_id = v_tenant
          and path is null
          and deleted_at is null;

        -- F2. Customers without a tenant belong to PlusClouds (single tenant before this change).
        update accounting_accounts
        set tenant_account_id = v_tenant
        where tenant_account_id is null;

        -- F3. Role assignments for ACTIVE partnerships, from the legacy role flags (same rule as the backfill, but
        --     only active partnerships get active roles).
        insert into accounting_partnership_role_assignments (tenant_account_id, partnership_id, partnership_role_id)
        select v_tenant, p.id, r.id
        from accounting_partnerships p
                 join accounting_accounts a on a.id = p.accounting_account_id
                 join accounting_partnership_roles r
                      on r.tenant_account_id = v_tenant and r.deleted_at is null
                          and ((r.slug = 'distributor' and a.is_distributor)
                              or (r.slug = 'integrator' and a.is_integrator)
                              or (r.slug = 'reseller' and a.is_reseller)
                              or (r.slug = 'affiliate' and a.is_affiliate))
        where p.deleted_at is null
          and p.tenant_account_id = v_tenant
          and p.status = 'active'
        on conflict do nothing;

        -- F4. Customer links from the old pointer columns for partners that now have a partnership row (D3).
        insert into accounting_account_partners
            (tenant_account_id, accounting_account_id, partnership_role_id, partnership_id)
        select v_tenant, c.id, r.id, p.id
        from accounting_accounts c
                 cross join lateral (values ('distributor', c.distributor_id),
                                            ('reseller', c.sales_partner_id),
                                            ('integrator', c.integrator_partner_id),
                                            ('affiliate', c.affiliate_partner_id)) as v(slug, partner_account_id)
                 join accounting_partnership_roles r
                      on r.tenant_account_id = v_tenant and r.slug = v.slug and r.deleted_at is null
                 join accounting_partnerships p
                      on p.accounting_account_id = v.partner_account_id
                          and p.tenant_account_id = v_tenant and p.deleted_at is null and p.status = 'active'
        where v.partner_account_id is not null
          and c.deleted_at is null
        on conflict do nothing;

        -- F5. Close role assignments and customer links that are still open on a partnership that is not active (D5b).
        update accounting_partnership_role_assignments ra
        set finished_at = now(), status = 'inactive'
        from accounting_partnerships p
        where p.id = ra.partnership_id
          and p.status <> 'active'
          and ra.finished_at is null
          and ra.deleted_at is null;

        update accounting_account_partners ap
        set finished_at = now()
        from accounting_partnerships p
        where p.id = ap.partnership_id
          and p.status <> 'active'
          and ap.finished_at is null
          and ap.deleted_at is null;

        select count(*) into v_count from accounting_partnerships where tenant_account_id = v_tenant and deleted_at is null;
        raise notice 'partnerships in tenant: %', v_count;
        select count(*) into v_count from accounting_partnership_role_assignments where tenant_account_id = v_tenant and finished_at is null;
        raise notice 'open role assignments: %', v_count;
        select count(*) into v_count from accounting_account_partners where tenant_account_id = v_tenant and finished_at is null;
        raise notice 'open customer links: %', v_count;
    end
$datafix$;

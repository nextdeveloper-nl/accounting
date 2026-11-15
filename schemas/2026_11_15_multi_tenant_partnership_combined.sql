-- PostgreSQL
-- COMBINED FILE: PART 1 = schema (idempotent DDL), PART 2 = PlusClouds backfill. Run top to bottom, never PART 2 alone.
-- On busy DBs run PART 1 statement by statement (autocommit); PART 2 is a single DO block.
-- Multi-tenant, role-generic partnership program for the Accounting module.
-- NOT applied automatically: review and apply manually (project rule: no Laravel migrations, no direct SQL from tooling).
-- Backfill for the PlusClouds tenant lives in 2026_11_15_multi_tenant_partnership_backfill.sql (run AFTER this file).
--
-- HOW TO RUN (avoids deadlocks on busy envs): run in AUTOCOMMIT mode, one statement at a time
-- (no wrapping BEGIN/COMMIT, e.g. `psql -f` default, not a single DBeaver transaction), ideally in a quiet window.
-- Every `references iam_accounts / iam_users / accounting_*` takes a SHARE ROW EXCLUSIVE lock on the referenced
-- (hot) table; inside one long transaction these locks pile up and can cycle with live app writes.
-- lock_timeout makes a blocked statement fail fast instead of deadlocking; just re-run the file (all statements are idempotent).
-- 30s: app transactions on iam_users/iam_accounts are short (seconds) but frequent on busy envs; the DDL must be able to wait
-- for them. While waiting, new queries on that table queue behind the DDL, so do not raise this much further.
set lock_timeout = '30s';

-- Conventions: all strings are text, timestamps are timestamptz, unstructured data is json,
-- tenant = IAM account (the "program owner"), public ids are exposed as *_id by the API layer.

-- ---------------------------------------------------------------------------------------------
-- 1. Partner programs: one row per tenant (program owner). Holds branding / notification settings.
-- ---------------------------------------------------------------------------------------------
create table if not exists accounting_partner_programs
(
    id                  bigserial primary key,
    uuid                uuid                     default gen_random_uuid() not null unique,
    tenant_account_id   bigint                                             not null unique
        references iam_accounts on delete cascade,
    responsible_email   text,
    notification_webhook text,
    -- logo_url, primary_color, display_name, support_email (used by branded emails)
    branding            json,
    default_country_code text,
    max_hierarchy_depth integer                  default 3                 not null,
    status              text                     default 'active'          not null,
    iam_account_id      bigint references iam_accounts on delete set null,
    iam_user_id         bigint references iam_users on delete set null,
    created_at          timestamp with time zone default now()             not null,
    updated_at          timestamp with time zone default now()             not null,
    deleted_at          timestamp with time zone
);

comment on table accounting_partner_programs is '[label:Partner program of a tenant (program owner)]';
comment on column accounting_partner_programs.tenant_account_id is '[label:IAM account that owns the program (tenant)]';
comment on column accounting_partner_programs.branding is '[label:Branding used in emails: logo_url, primary_color, display_name, support_email]';
comment on column accounting_partner_programs.max_hierarchy_depth is '[label:Max depth of sub-partner hierarchy]';

-- ---------------------------------------------------------------------------------------------
-- 2. Generic partnership roles, defined per tenant.
-- ---------------------------------------------------------------------------------------------
create table if not exists accounting_partnership_roles
(
    id                          bigserial primary key,
    uuid                        uuid                     default gen_random_uuid() not null unique,
    tenant_account_id           bigint                                             not null
        references iam_accounts on delete cascade,
    slug                        text                                               not null,
    name                        text                                               not null,
    description                 text,
    commission_rate             numeric(8, 4)            default 0                 not null,
    -- rate earned by the parent partner when a sub-partner with this role earns commission
    parent_override_rate        numeric(8, 4)            default 0                 not null,
    commission_basis            text                     default 'invoice_amount'  not null,
    priority                    integer                  default 0                 not null,
    allows_multiple_per_customer boolean                 default false             not null,
    is_default_for_signup_code  boolean                  default false             not null,
    -- role ids (json array) a holder of this role may grant to sub-partners
    allowed_child_role_ids      json,
    -- e.g. {"can_resell":true,"can_invite_sub_partners":true}
    permissions                 json,
    status                      text                     default 'active'          not null,
    iam_account_id              bigint references iam_accounts on delete set null,
    iam_user_id                 bigint references iam_users on delete set null,
    created_at                  timestamp with time zone default now()             not null,
    updated_at                  timestamp with time zone default now()             not null,
    deleted_at                  timestamp with time zone
);

comment on table accounting_partnership_roles is '[label:Tenant defined partnership roles (distributor, reseller, ...)]';

create unique index if not exists uq_accounting_partnership_roles_tenant_slug
    on accounting_partnership_roles (tenant_account_id, slug) where (deleted_at is null);

-- ---------------------------------------------------------------------------------------------
-- 3. accounting_partnerships: tenant, invitation lifecycle, hierarchy.
--    (Table already exists in the live DB; only additive changes here.)
-- ---------------------------------------------------------------------------------------------
alter table accounting_partnerships
    add column if not exists tenant_account_id        bigint references iam_accounts on delete cascade,
    add column if not exists status                   text default 'pending' not null, -- pending|active|rejected|revoked (fail closed)
    add column if not exists invited_at               timestamp with time zone,
    add column if not exists accepted_at              timestamp with time zone,
    add column if not exists invited_by_iam_user_id   bigint references iam_users on delete set null,
    add column if not exists parent_partnership_id    bigint references accounting_partnerships on delete set null,
    add column if not exists path                     text, -- materialised path e.g. 1.5.9 (ids of ancestors + self)
    add column if not exists iam_user_id              bigint references iam_users on delete set null;

comment on column accounting_partnerships.tenant_account_id is '[label:Tenant (program owner) this partnership belongs to]';
comment on column accounting_partnerships.status is '[label:pending, active, rejected, revoked]';
comment on column accounting_partnerships.parent_partnership_id is '[label:Parent partner for sub-partners]';
comment on column accounting_partnerships.path is '[label:Materialised hierarchy path of partnership ids]';

-- partner_code is globally unique: tenant is derived from the code at signup.
create unique index if not exists uq_accounting_partnerships_partner_code
    on accounting_partnerships (partner_code) where (deleted_at is null and partner_code is not null);
create unique index if not exists uq_accounting_partnerships_tenant_account
    on accounting_partnerships (tenant_account_id, accounting_account_id) where (deleted_at is null);
create index if not exists idx_accounting_partnerships_parent
    on accounting_partnerships (parent_partnership_id) where (deleted_at is null);
create index if not exists idx_accounting_partnerships_path
    on accounting_partnerships (path text_pattern_ops) where (deleted_at is null);

-- ---------------------------------------------------------------------------------------------
-- 4. Partner <-> role assignments.
-- ---------------------------------------------------------------------------------------------
create table if not exists accounting_partnership_role_assignments
(
    id                    bigserial primary key,
    uuid                  uuid                     default gen_random_uuid() not null unique,
    tenant_account_id     bigint                                             not null
        references iam_accounts on delete cascade,
    partnership_id        bigint                                             not null
        references accounting_partnerships on delete cascade,
    partnership_role_id   bigint                                             not null
        references accounting_partnership_roles on delete cascade,
    status                text                     default 'active'          not null,
    started_at            timestamp with time zone default now()             not null,
    finished_at           timestamp with time zone,
    iam_account_id        bigint references iam_accounts on delete set null,
    iam_user_id           bigint references iam_users on delete set null,
    created_at            timestamp with time zone default now()             not null,
    updated_at            timestamp with time zone default now()             not null,
    deleted_at            timestamp with time zone
);

comment on table accounting_partnership_role_assignments is '[label:Roles held by a partner inside a tenant]';

create unique index if not exists uq_accounting_partnership_role_assignments_active
    on accounting_partnership_role_assignments (partnership_id, partnership_role_id)
    where (deleted_at is null and finished_at is null);

-- ---------------------------------------------------------------------------------------------
-- 5. Customer <-> partner link per role (replaces distributor_id / sales_partner_id /
--    integrator_partner_id / affiliate_partner_id; those stay dual-written until switch-over).
-- ---------------------------------------------------------------------------------------------
create table if not exists accounting_account_partners
(
    id                    bigserial primary key,
    uuid                  uuid                     default gen_random_uuid() not null unique,
    tenant_account_id     bigint                                             not null
        references iam_accounts on delete cascade,
    accounting_account_id bigint                                             not null
        references accounting_accounts on delete cascade,
    partnership_role_id   bigint                                             not null
        references accounting_partnership_roles on delete cascade,
    partnership_id        bigint                                             not null
        references accounting_partnerships on delete cascade,
    started_at            timestamp with time zone default now()             not null,
    finished_at           timestamp with time zone,
    iam_account_id        bigint references iam_accounts on delete set null,
    iam_user_id           bigint references iam_users on delete set null,
    created_at            timestamp with time zone default now()             not null,
    updated_at            timestamp with time zone default now()             not null,
    deleted_at            timestamp with time zone
);

comment on table accounting_account_partners is '[label:Partner serving a customer account for a given role]';

create unique index if not exists uq_accounting_account_partners_active
    on accounting_account_partners (accounting_account_id, partnership_role_id)
    where (deleted_at is null and finished_at is null);
create index if not exists idx_accounting_account_partners_partnership
    on accounting_account_partners (partnership_id) where (deleted_at is null);

-- ---------------------------------------------------------------------------------------------
-- 6. Tenant per-country default partner per role (replaces leo.providers.zones config).
-- ---------------------------------------------------------------------------------------------
create table if not exists accounting_partner_program_zones
(
    id                      bigserial primary key,
    uuid                    uuid                     default gen_random_uuid() not null unique,
    tenant_account_id       bigint                                             not null
        references iam_accounts on delete cascade,
    partner_program_id      bigint                                             not null
        references accounting_partner_programs on delete cascade,
    common_country_id       bigint,   -- null = global fallback for the role
    partnership_role_id     bigint                                             not null
        references accounting_partnership_roles on delete cascade,
    default_partnership_id  bigint                                             not null
        references accounting_partnerships on delete cascade,
    iam_account_id          bigint references iam_accounts on delete set null,
    iam_user_id             bigint references iam_users on delete set null,
    created_at              timestamp with time zone default now()             not null,
    updated_at              timestamp with time zone default now()             not null,
    deleted_at              timestamp with time zone
);

comment on table accounting_partner_program_zones is '[label:Default partner per country and role for a tenant]';

create unique index if not exists uq_accounting_partner_program_zones
    on accounting_partner_program_zones (partner_program_id, common_country_id, partnership_role_id)
    where (deleted_at is null);

-- ---------------------------------------------------------------------------------------------
-- 7. Zones a partner is responsible for.
-- ---------------------------------------------------------------------------------------------
create table if not exists accounting_partnership_zones
(
    id                  bigserial primary key,
    uuid                uuid                     default gen_random_uuid() not null unique,
    tenant_account_id   bigint                                             not null
        references iam_accounts on delete cascade,
    partnership_id      bigint                                             not null
        references accounting_partnerships on delete cascade,
    partnership_role_id bigint references accounting_partnership_roles on delete cascade, -- null = all roles of the partner
    common_country_id   bigint                                             not null,
    common_city_id      bigint,
    region              text,
    is_exclusive        boolean                  default false             not null,
    priority            integer                  default 0                 not null,
    status              text                     default 'active'          not null,
    iam_account_id      bigint references iam_accounts on delete set null,
    iam_user_id         bigint references iam_users on delete set null,
    created_at          timestamp with time zone default now()             not null,
    updated_at          timestamp with time zone default now()             not null,
    deleted_at          timestamp with time zone
);

comment on table accounting_partnership_zones is '[label:Zones (country/city) a partner is responsible for]';

create index if not exists idx_accounting_partnership_zones_lookup
    on accounting_partnership_zones (tenant_account_id, common_country_id) where (deleted_at is null);
create index if not exists idx_accounting_partnership_zones_partnership
    on accounting_partnership_zones (partnership_id) where (deleted_at is null);

-- ---------------------------------------------------------------------------------------------
-- 8. Tenant on customer accounts and on the assignment audit log.
-- ---------------------------------------------------------------------------------------------
alter table accounting_accounts
    add column if not exists tenant_account_id bigint references iam_accounts on delete set null;
comment on column accounting_accounts.tenant_account_id is '[label:Tenant (program owner) this customer belongs to]';
create index if not exists idx_accounting_accounts_tenant on accounting_accounts (tenant_account_id) where (deleted_at is null);

alter table accounting_partner_assignments
    add column if not exists tenant_account_id    bigint references iam_accounts on delete set null,
    add column if not exists partnership_role_id  bigint references accounting_partnership_roles on delete set null;
comment on column accounting_partner_assignments.partnership_role_id is '[label:Generic role of the assignment; legacy rows keep type]';

-- ---------------------------------------------------------------------------------------------
-- 9. Generic commission records (per role and hierarchy level).
-- ---------------------------------------------------------------------------------------------
create table if not exists accounting_invoice_commissions
(
    id                          bigserial primary key,
    uuid                        uuid                     default gen_random_uuid() not null unique,
    tenant_account_id           bigint                                             not null
        references iam_accounts on delete cascade,
    accounting_invoice_id       bigint                                             not null
        references accounting_invoices on delete cascade,
    partnership_role_id         bigint                                             not null
        references accounting_partnership_roles on delete cascade,
    partnership_id              bigint                                             not null
        references accounting_partnerships on delete cascade,
    level                       integer                  default 0                 not null, -- 0 = direct partner, 1 = parent override, ...
    commission_invoice_id       bigint
        references accounting_invoices on delete set null,
    iam_account_id              bigint references iam_accounts on delete set null,
    iam_user_id                 bigint references iam_users on delete set null,
    created_at                  timestamp with time zone default now()             not null,
    updated_at                  timestamp with time zone default now()             not null,
    deleted_at                  timestamp with time zone
);

comment on table accounting_invoice_commissions is '[label:Commission generated for a partner from a paid invoice]';

-- one commission per invoice/role/partner/level: makes the commission job idempotent
create unique index if not exists uq_accounting_invoice_commissions
    on accounting_invoice_commissions (accounting_invoice_id, partnership_role_id, partnership_id, level)
    where (deleted_at is null);

-- =============================================================================================
-- PART 2: BACKFILL of the PlusClouds tenant (runs after all objects above exist).
-- Change v_tenant if the PlusClouds IAM account id is not 5 (config leo.providers.zones).
-- Single DO block = one transaction: it either completes fully or changes nothing. Safe to re-run.
-- =============================================================================================
do
$backfill$
    declare
        v_tenant constant bigint := 5; -- iam_accounts.id of the PlusClouds tenant
        v_count bigint;
    begin
        if not exists (select 1 from iam_accounts where id = v_tenant) then
            raise exception 'iam_accounts.id % does not exist, fix v_tenant', v_tenant;
        end if;

        -- 1. Program of the PlusClouds tenant.
        insert into accounting_partner_programs (tenant_account_id, responsible_email, status)
        values (v_tenant, 'tevfik.porsuk@plusclouds.com', 'active')
        on conflict (tenant_account_id) do nothing;

        -- 2. Default roles mirroring app/Enums/CommissionType.php (distributor/integrator/reseller 10%, affiliate 3%).
        --    priority mirrors the documented order Partner > Distributor > Affiliate > Sales Partner (higher = wins).
        insert into accounting_partnership_roles
            (tenant_account_id, slug, name, commission_rate, priority, is_default_for_signup_code, permissions)
        select v_tenant, r.slug, r.name, r.rate, r.priority, r.signup, r.permissions::json
        from (values ('integrator', 'Integrator', 0.10, 40, false, '{"can_resell":false,"can_invite_sub_partners":false}'),
                     ('distributor', 'Distributor', 0.10, 30, false, '{"can_resell":true,"can_invite_sub_partners":true}'),
                     ('affiliate', 'Affiliate', 0.03, 20, true, '{"can_resell":false,"can_invite_sub_partners":false}'),
                     ('reseller', 'Reseller', 0.10, 10, false, '{"can_resell":true,"can_invite_sub_partners":false}')
             ) as r(slug, name, rate, priority, signup, permissions)
        where not exists (select 1
                          from accounting_partnership_roles x
                          where x.tenant_account_id = v_tenant and x.slug = r.slug and x.deleted_at is null);

        -- 3. Every existing partnership belongs to the PlusClouds tenant and is already approved/active.
        -- Approved partnerships, or ones whose account already holds a partner role flag (that is what drives
        -- commissions today), stay active; unapproved applicants become pending. Soft-deleted rows are skipped.
        update accounting_partnerships p
        set tenant_account_id = v_tenant,
            status            = case when coalesce(p.is_approved, false)
                                          or exists (select 1 from accounting_accounts a
                                                     where a.id = p.accounting_account_id
                                                       and (a.is_distributor or a.is_integrator or a.is_reseller or a.is_affiliate))
                                     then 'active' else 'pending' end,
            path              = p.id::text
        where p.tenant_account_id is null
          and p.deleted_at is null;

        -- 4. Role flags on accounting_accounts -> role assignments (is_reseller = legacy sales partner flag).
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
          and p.status = 'active'
        on conflict do nothing;

        -- 5. Customer pointer columns -> accounting_account_partners.
        --    The pointer columns reference accounting_accounts.id (the partner's account); map to that account's partnership.
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
                          and p.tenant_account_id = v_tenant and p.deleted_at is null
        where v.partner_account_id is not null
          and c.deleted_at is null
        on conflict do nothing;

        -- 6. Customers belong to the PlusClouds tenant.
        update accounting_accounts set tenant_account_id = v_tenant where tenant_account_id is null;

        -- 7. Assignment audit log: attach tenant + generic role for legacy rows.
        update accounting_partner_assignments pa
        set tenant_account_id   = v_tenant,
            partnership_role_id = r.id
        from accounting_partnership_roles r
        where pa.tenant_account_id is null
          and r.tenant_account_id = v_tenant
          and r.deleted_at is null
          and r.slug = pa.type;

        -- Summary (visible in DBeaver "Output" / psql notices)
        select count(*) into v_count from accounting_partnership_roles where tenant_account_id = v_tenant and deleted_at is null;
        raise notice 'roles: %', v_count;
        select count(*) into v_count from accounting_partnership_role_assignments where tenant_account_id = v_tenant;
        raise notice 'role assignments: %', v_count;
        select count(*) into v_count from accounting_account_partners where tenant_account_id = v_tenant;
        raise notice 'customer partner links: %', v_count;
        select count(*) into v_count from accounting_partnerships where tenant_account_id = v_tenant;
        raise notice 'partnerships in tenant: %', v_count;
    end
$backfill$;

-- Per-country defaults (leo.providers.zones) are environment specific: insert them by hand, e.g.
-- insert into accounting_partner_program_zones
--     (tenant_account_id, partner_program_id, common_country_id, partnership_role_id, default_partnership_id)
-- values (...);

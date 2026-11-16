-- PostgreSQL
-- Partner directory visibility: who may see a partner in the partner lists (distributors / integrators / sales partners /
-- affiliates / vendors perspectives). Run manually, statement by statement (autocommit), after the multi-tenant files.
--
--   private   only the partner itself and the tenant that owns the partnership see it (default for NEW partnerships)
--   customers also the customers that are served by this partner (accounting_account_partners)
--   public    every account sees it in the partner lists
--
-- Before this column every partner was visible to every account, so the partnerships that already exist are set to
-- 'public' once to keep today's behaviour. They are recognised by invited_at IS NULL (rows created by the new invitation
-- flow always have invited_at set), so re-running this file never flips a partnership somebody made private.

alter table accounting_partnerships
    add column if not exists visibility text not null default 'private';

comment on column accounting_partnerships.visibility is '[label:Who sees this partner in the partner lists: private, customers, public]';

alter table accounting_partnerships
    add constraint chk_accounting_partnerships_visibility
        check (visibility in ('private', 'customers', 'public')) not valid;
alter table accounting_partnerships validate constraint chk_accounting_partnerships_visibility;

update accounting_partnerships
set visibility = 'public'
where visibility = 'private'
  and invited_at is null
  and deleted_at is null;

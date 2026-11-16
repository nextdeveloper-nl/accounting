-- PostgreSQL
-- Commission rates of a partnership role are fractions: 0 = 0%, 1 = 100%. They are typed in by tenants and every
-- commission becomes a payable invoice, so the database refuses nonsense as a last line of defence (the API request
-- rules and the commission engine enforce the same bounds, and the engine additionally caps one invoice's total payout).
-- Run manually, statement by statement (autocommit).

-- D1. Roles that would violate the check (must be empty before the constraint can be validated).
select id, tenant_account_id, slug, commission_rate, parent_override_rate
from accounting_partnership_roles
where deleted_at is null
  and (commission_rate < 0 or commission_rate > 1 or parent_override_rate < 0 or parent_override_rate > 1);

alter table accounting_partnership_roles
    add constraint chk_accounting_partnership_roles_rates
        check (commission_rate between 0 and 1 and parent_override_rate between 0 and 1) not valid;
alter table accounting_partnership_roles validate constraint chk_accounting_partnership_roles_rates;

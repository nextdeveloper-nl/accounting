-- PostgreSQL
-- TRIGGERS (introspected live from leo_v4 via pg_trigger/pg_proc; no triggers
-- are defined directly on any accounting_* table. The only trigger relevant
-- to this module lives on iam_accounts and seeds accounting_accounts on
-- account creation, mirroring the equivalent pattern used by the IAAS/CRM/
-- Communication modules.)

create or replace function create_accounting_accounts()
    returns trigger
    language plpgsql
as
$function$
BEGIN
    insert into accounting_accounts (iam_account_id, common_currency_id)
    values (new.id, (select id from common_currencies where code = 'USD' limit 1))
    ON CONFLICT DO NOTHING;

    return new;
end;
$function$;

create trigger trigger_create_accounting_accounts
    after insert
    on iam_accounts
    for each row
execute function create_accounting_accounts();

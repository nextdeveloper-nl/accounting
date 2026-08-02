-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL; matches current
-- NextDeveloper\Accounting\Database\Models\PartnerAssignments fillable list as-is, no drift found.)

create table if not exists accounting_partner_assignments
(
    id                    bigserial primary key,
    uuid                  uuid                     default gen_random_uuid() not null
        unique,

    accounting_account_id bigint                                             not null
        references accounting_accounts
            on delete cascade,

    type                  text                                               not null,
    old_partner_id        bigint,
    new_partner_id        bigint,
    started_at            timestamp with time zone default now()             not null,
    finished_at           timestamp,
    iam_user_id           bigint
        references iam_users
            on delete set null,
    iam_account_id        bigint
        references iam_accounts
            on delete set null,
    reason                text,
    created_at            timestamp with time zone default now()             not null,
    updated_at            timestamp with time zone default now()             not null,
    deleted_at            timestamp
);

comment on table accounting_partner_assignments is '[label:Logs changes to partners associated with accounting accounts]';
comment on column accounting_partner_assignments.type is '[label:Type of partner: distributor, integrator, reseller]';
comment on column accounting_partner_assignments.old_partner_id is '[alias:accounting_account_id][label:ID of old partner]';
comment on column accounting_partner_assignments.new_partner_id is '[alias:accounting_account_id][label:ID of the new partner]';
comment on column accounting_partner_assignments.started_at is '[label:Timestamp when the partner assignment started]';
comment on column accounting_partner_assignments.finished_at is '[label:Timestamp when the partner assignment finished]';
comment on column accounting_partner_assignments.iam_user_id is '[label:ID of the IAM user who made the change]';
comment on column accounting_partner_assignments.iam_account_id is '[label:ID of the IAM account associated with the change]';
comment on column accounting_partner_assignments.reason is '[label:Reason for the partner change]';

create index if not exists idx_accounting_partner_assignments_account
    on accounting_partner_assignments (accounting_account_id)
    where (deleted_at IS NULL);

create index if not exists idx_accounting_partner_assignments_type
    on accounting_partner_assignments (type)
    where (deleted_at IS NULL);

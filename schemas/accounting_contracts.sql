-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL; matches current
-- NextDeveloper\Accounting\Database\Models\Contracts fillable list as-is, no drift found.)

create table if not exists accounting_contracts
(
    id                    bigserial primary key,
    uuid                  uuid        default gen_random_uuid(),

    accounting_account_id bigint                                not null,

    name                  text                                  not null,
    description           text                                  null,

    term_starts           timestamptz                           not null,
    term_ends             timestamptz                           not null,

    is_approved           boolean     default false,
    is_signed              boolean     default false,

    iam_account_id        bigint                                not null,
    iam_user_id            bigint                                not null,

    common_currency_id    bigint                                null,

    created_at            timestamptz default CURRENT_TIMESTAMP not null,
    updated_at            timestamptz default CURRENT_TIMESTAMP not null,
    deleted_at            timestamptz                           null
);

comment on column accounting_contracts.common_currency_id is '[label:If the common_currency_id is null then the invoice will get the providers currency.]';

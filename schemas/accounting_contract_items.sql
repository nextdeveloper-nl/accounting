-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL; matches current
-- NextDeveloper\Accounting\Database\Models\ContractItems fillable list as-is, no drift found.)

create table if not exists accounting_contract_items
(
    id                     bigserial primary key,
    uuid                   uuid           default gen_random_uuid(),

    object_type            text                                     not null,
    object_id              bigint                                   not null,

    accounting_account_id  bigint                                   not null,
    accounting_contract_id bigint                                   not null,

    contract_type          text           default 'discount',

    price                  numeric(10, 4) default null,
    discount               integer                                  null,
    common_currency_id     bigint                                   not null,

    iam_account_id         bigint                                   not null,
    iam_user_id             bigint                                   not null,

    created_at             timestamptz    default CURRENT_TIMESTAMP not null,
    updated_at             timestamptz    default CURRENT_TIMESTAMP not null,
    deleted_at             timestamptz                              null
);

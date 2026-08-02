-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL, corrected to match current
-- NextDeveloper\Accounting\Database\Models\InvoiceItems fillable list: added `discount`,
-- which the model has but the legacy DDL didn't.)

create table if not exists accounting_invoice_items
(
    id                          bigserial primary key,
    uuid                        uuid        default gen_random_uuid(),

    object_type                 text                                  not null,
    object_id                   bigint                                not null,

    quantity                    int         default 1,
    unit_price                  numeric(10, 4)                        not null,
    discount                    integer                               null,

    iam_account_id              bigint                                not null,

    accounting_promo_code_id    bigint                                null,

    total_price                 numeric(10, 4) GENERATED ALWAYS AS ( quantity * unit_price ) STORED,
    common_currency_id          bigint                                not null,

    details                     json                                  null,

    accounting_invoice_id       bigint                                not null,
    accounting_account_id       bigint                                not null,
    accounting_contract_item_id bigint                                null,

    created_at                  timestamptz default CURRENT_TIMESTAMP not null,
    updated_at                  timestamptz default CURRENT_TIMESTAMP not null,
    deleted_at                  timestamptz                           null
);

comment on column accounting_invoice_items.accounting_promo_code_id is '[ro]';

-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL; matches current
-- NextDeveloper\Accounting\Database\Models\Invoices fillable list as-is, no drift found.)

create table if not exists accounting_invoices
(
    id                                bigserial primary key,
    uuid                              uuid           default gen_random_uuid(),

    accounting_account_id             bigint                                   not null,
    invoice_number                    text                                     null,

    exchange_rate                     json                                     null,
    amount                            numeric(10, 4) default 0                 not null,
    common_currency_id                bigint                                   not null,
    vat                               numeric(10, 4)                           not null,

    is_paid                           bool           default false,
    is_refund                         bool           default false,

    due_date                          timestamptz                              not null,

    iam_account_id                    bigint                                   not null,
    iam_user_id                       bigint                                   not null,

    is_payable                        bool           default false,
    is_sealed                         bool           default false,

    note                              text           default false,

    term_year                         integer                                  null,
    term_month                        integer                                  null,

    is_cancelled                      bool           default false,
    cancellation_reason               text,

    payment_link_url                  text,

    is_commission_invoice             boolean        default false,
    distributor_commission_invoice_id bigint         default 0,
    integrator_commission_invoice_id  bigint         default 0,
    reseller_commission_invoice_id    bigint         default 0,
    affiliate_commission_invoice_id   bigint         default 0,

    created_at                        timestamptz    default CURRENT_TIMESTAMP not null,
    updated_at                        timestamptz    default CURRENT_TIMESTAMP not null,
    deleted_at                        timestamptz                              null
);

comment on column accounting_invoices.distributor_commission_invoice_id is '[alias:accounting_invoices_id]';
comment on column accounting_invoices.integrator_commission_invoice_id is '[alias:accounting_invoices_id]';
comment on column accounting_invoices.reseller_commission_invoice_id is '[alias:accounting_invoices_id]';
comment on column accounting_invoices.affiliate_commission_invoice_id is '[alias:accounting_invoices_id]';

-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL, corrected to match current
-- NextDeveloper\Accounting\Database\Models\PaymentGateways fillable list: added
-- accounting_account_id, common_currency_id, vat_rate, which the model has but the
-- legacy DDL didn't.)

create table if not exists accounting_payment_gateways
(
    id                bigserial primary key,
    uuid              uuid           default gen_random_uuid(),

    name              text                                     not null,

    gateway           text                                     not null,
    parameters        json                                     not null,

    is_active         boolean        default true,
    common_country_id bigint                                   not null,
    common_currency_id bigint                                  null,
    vat_rate          numeric(5, 2)                             null,

    iam_account_id    bigint                                   not null,
    accounting_account_id bigint                                null,

    created_at        timestamptz    default CURRENT_TIMESTAMP not null,
    updated_at        timestamptz    default CURRENT_TIMESTAMP not null,
    deleted_at        timestamptz                              null
);

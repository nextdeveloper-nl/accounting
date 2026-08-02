-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL; matches current
-- NextDeveloper\Accounting\Database\Models\PromoCodes fillable list as-is, no drift found.)

create table if not exists accounting_promo_codes
(
    id                 bigserial primary key,
    uuid               uuid        default gen_random_uuid(),

    code               text                                  not null,

    iam_account_id     bigint                                null,
    iam_user_id        bigint                                null,

    value              integer                               not null,
    common_currency_id bigint                                not null,

    gift_code_data     text,

    created_at         timestamptz default CURRENT_TIMESTAMP not null,
    updated_at         timestamptz default CURRENT_TIMESTAMP not null,
    deleted_at         timestamptz                           null
);

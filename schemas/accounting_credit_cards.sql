-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL, corrected to match current
-- NextDeveloper\Accounting\Database\Models\CreditCards fillable list: added
-- pg_card_user_key, pg_card_token, pg_provider, is_stored_at_pg, which the model
-- has but the legacy DDL didn't.)

create table if not exists accounting_credit_cards
(
    id             bigserial primary key,
    uuid           uuid        default gen_random_uuid(),

    name           text                                  not null,

    type           text                                  null,
    cc_holder_name text                                  not null,
    cc_number      text                                  not null,
    cc_month       text                                  not null,
    cc_year        text                                  not null,
    cc_cvv         text                                  not null,

    is_default     boolean     default false,
    is_valid       boolean     default false,
    is_active      boolean     default false,

    is_3d_secure   boolean     default false,

    pg_card_user_key text                                null,
    pg_card_token    text                                null,
    pg_provider      text                                null,
    is_stored_at_pg  boolean   default false,

    iam_account_id bigint                                not null,
    iam_user_id    bigint                                not null,

    created_at     timestamptz default CURRENT_TIMESTAMP not null,
    updated_at     timestamptz default CURRENT_TIMESTAMP not null,
    deleted_at     timestamptz                           null
);

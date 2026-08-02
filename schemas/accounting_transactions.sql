-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL, corrected to match current
-- NextDeveloper\Accounting\Database\Models\Transactions fillable list:
-- conversation_id -> conversation_identifier.)

create table if not exists accounting_transactions
(
    id                            bigserial primary key,
    uuid                          uuid        default gen_random_uuid(),

    accounting_invoice_id         bigint                                not null,
    amount                        numeric(10, 4)                        not null,
    common_currency_id            bigint                                not null,

    accounting_payment_gateway_id bigint                                not null,

    iam_account_id                bigint                                not null,
    accounting_account_id         bigint                                not null,

    gateway_response              text                                  null,
    conversation_identifier       text                                  not null,

    is_pending                    boolean     default true,

    created_at                    timestamptz default CURRENT_TIMESTAMP not null,
    updated_at                    timestamptz default CURRENT_TIMESTAMP not null,
    deleted_at                    timestamptz                           null
);

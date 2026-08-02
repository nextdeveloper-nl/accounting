-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL, corrected to match current
-- NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions fillable list:
-- accounting_account_id -> accounting_invoice_id.)

create table if not exists accounting_payment_checkout_sessions
(
    id                            bigserial primary key,
    uuid                          uuid        default gen_random_uuid(),

    accounting_payment_gateway_id bigint                                not null,
    accounting_invoice_id         bigint                                not null,
    payment_data                  json                                  null,
    session_data                  json                                  null,
    is_invalidated                boolean     default false,

    created_at                    timestamptz default CURRENT_TIMESTAMP not null,
    updated_at                    timestamptz default CURRENT_TIMESTAMP not null,
    deleted_at                    timestamptz                           null
);

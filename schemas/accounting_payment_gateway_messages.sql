-- PostgreSQL
-- TABLE (reconstructed from database.leo.v4 reference DDL; matches current
-- NextDeveloper\Accounting\Database\Models\PaymentGatewayMessages fillable list as-is, no drift found.)

create table if not exists accounting_payment_gateway_messages
(
    id                            bigserial primary key,
    uuid                          uuid        default gen_random_uuid(),

    message_identifier            text                                  null,
    message                       text                                  null,

    accounting_payment_gateway_id bigint                                not null,

    created_at                    timestamptz default CURRENT_TIMESTAMP not null,
    updated_at                    timestamptz default CURRENT_TIMESTAMP not null,
    deleted_at                    timestamptz                           null
);

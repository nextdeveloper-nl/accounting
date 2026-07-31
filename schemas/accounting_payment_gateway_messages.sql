-- PostgreSQL

CREATE TABLE accounting_payment_gateway_messages (
    id                             bigint NOT NULL DEFAULT nextval('accounting_payment_gateway_messages_id_seq'::regclass),
    uuid                           uuid DEFAULT gen_random_uuid(),
    message_identifier             text,
    message                        text,
    accounting_payment_gateway_id  bigint NOT NULL,
    created_at                     timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                     timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                     timestamp with time zone,
    CONSTRAINT accounting_payment_gateway_messages_pkey PRIMARY KEY (id)
);

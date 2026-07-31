-- PostgreSQL

CREATE TABLE accounting_payment_checkout_sessions (
    id                             bigint NOT NULL DEFAULT nextval('accounting_payment_checkout_sessions_id_seq'::regclass),
    uuid                           uuid DEFAULT gen_random_uuid(),
    accounting_payment_gateway_id  bigint NOT NULL,
    accounting_invoice_id          bigint NOT NULL,
    payment_data                   json,
    session_data                   json,
    is_invalidated                 boolean DEFAULT false,
    created_at                     timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                     timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                     timestamp with time zone,
    CONSTRAINT accounting_payment_checkout_sessions_pkey PRIMARY KEY (id)
);

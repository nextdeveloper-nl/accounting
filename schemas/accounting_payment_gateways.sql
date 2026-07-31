-- PostgreSQL

CREATE TABLE accounting_payment_gateways (
    id                     bigint NOT NULL DEFAULT nextval('accounting_payment_gateways_id_seq'::regclass),
    uuid                   uuid DEFAULT gen_random_uuid(),
    name                   text NOT NULL,
    gateway                text NOT NULL,
    is_active              boolean DEFAULT true,
    common_country_id      bigint NOT NULL,
    iam_account_id         bigint NOT NULL,
    created_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at             timestamp with time zone,
    parameters             json,
    common_currency_id     bigint,
    vat_rate               double precision,
    accounting_account_id  bigint,
    CONSTRAINT accounting_payment_gateways_pkey PRIMARY KEY (id)
);

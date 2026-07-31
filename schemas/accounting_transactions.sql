-- PostgreSQL

CREATE TABLE accounting_transactions (
    id                             bigint NOT NULL DEFAULT nextval('accounting_transactions_id_seq'::regclass),
    uuid                           uuid DEFAULT gen_random_uuid(),
    accounting_invoice_id          bigint NOT NULL,
    amount                         numeric(10,4) NOT NULL,
    common_currency_id             bigint NOT NULL,
    accounting_payment_gateway_id  bigint NOT NULL,
    iam_account_id                 bigint NOT NULL,
    accounting_account_id          bigint NOT NULL,
    gateway_response               text,
    created_at                     timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                     timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                     timestamp with time zone,
    conversation_identifier        text NOT NULL,
    is_pending                     boolean DEFAULT true,
    CONSTRAINT accounting_transactions_pkey PRIMARY KEY (id)
);

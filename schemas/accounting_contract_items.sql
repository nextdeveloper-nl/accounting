-- PostgreSQL

CREATE TABLE accounting_contract_items (
    id                      bigint NOT NULL DEFAULT nextval('accounting_contract_items_id_seq'::regclass),
    uuid                    uuid DEFAULT gen_random_uuid(),
    object_type             text NOT NULL,
    object_id               bigint NOT NULL,
    iam_account_id          bigint NOT NULL,
    iam_user_id             bigint NOT NULL,
    created_at              timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at              timestamp with time zone,
    accounting_contract_id  bigint NOT NULL,
    accounting_account_id   bigint NOT NULL,
    price                   numeric(10,4) DEFAULT NULL::numeric,
    discount                integer,
    common_currency_id      bigint,
    contract_type           text DEFAULT 'discount'::text,
    CONSTRAINT accounting_contract_items_pkey PRIMARY KEY (id)
);

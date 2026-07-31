-- PostgreSQL

CREATE TABLE accounting_contracts (
    id                     bigint NOT NULL DEFAULT nextval('accounting_contracts_id_seq'::regclass),
    uuid                   uuid DEFAULT gen_random_uuid(),
    accounting_account_id  bigint NOT NULL,
    name                   text NOT NULL,
    description            text,
    term_starts            timestamp with time zone NOT NULL,
    term_ends              timestamp with time zone NOT NULL,
    iam_account_id         bigint NOT NULL,
    iam_user_id            bigint NOT NULL,
    created_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at             timestamp with time zone,
    is_approved            boolean DEFAULT false,
    is_signed              boolean DEFAULT false,
    CONSTRAINT accounting_contracts_pkey PRIMARY KEY (id)
);

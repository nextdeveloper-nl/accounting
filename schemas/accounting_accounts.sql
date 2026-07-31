-- PostgreSQL

CREATE TABLE accounting_accounts (
    id                     bigint NOT NULL DEFAULT nextval('accounting_accounts_id_seq'::regclass),
    uuid                   uuid DEFAULT gen_random_uuid(),
    iam_account_id         bigint NOT NULL, -- [ro]
    tax_office             text,
    tax_number             text,
    accounting_identifier  text,
    credit                 numeric(10,2) DEFAULT 0,
    common_currency_id     bigint NOT NULL,
    created_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at             timestamp with time zone,
    trade_office_number    text,
    trade_office           text,
    tr_mersis              text,
    is_suspended           boolean DEFAULT false,
    balance                numeric(10,4),
    is_disabled            boolean DEFAULT false,
    distributor_id         bigint, -- [alias:accounting_account_id]
    sales_partner_id       bigint, -- [alias:accounting_account_id]
    integrator_partner_id  bigint, -- [alias:accounting_account_id]
    affiliate_partner_id   bigint, -- [alias:accounting_account_id]
    is_distributor         boolean DEFAULT false,
    is_integrator          boolean DEFAULT false,
    is_vendor              boolean DEFAULT false,
    is_reseller            boolean DEFAULT false,
    is_affiliate           boolean DEFAULT false,
    affiliate_level        integer DEFAULT 1,
    partner_code           text,
    mapping                json DEFAULT '[]'::json,
    CONSTRAINT accounting_accounts_pkey PRIMARY KEY (id),
    CONSTRAINT accounting_accounts_iam_accounts_pk UNIQUE (iam_account_id)
);

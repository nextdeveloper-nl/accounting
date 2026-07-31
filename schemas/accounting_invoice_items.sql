-- PostgreSQL

CREATE TABLE accounting_invoice_items (
    id                        bigint NOT NULL DEFAULT nextval('accounting_invoice_items_id_seq'::regclass),
    uuid                      uuid DEFAULT gen_random_uuid(),
    object_type               text NOT NULL,
    object_id                 bigint NOT NULL,
    quantity                  integer DEFAULT 1,
    unit_price                numeric(10,4) NOT NULL,
    common_currency_id        bigint NOT NULL,
    created_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                timestamp with time zone,
    iam_account_id            bigint NOT NULL DEFAULT 0,
    accounting_invoice_id     bigint,
    accounting_promo_code_id  bigint, -- [ro]
    accounting_account_id     bigint,
    details                   json,
    discount                  numeric(5,2) DEFAULT 0.00, -- Discount percentage applied to the item price
    total_price               numeric(10,4) DEFAULT (((quantity)::numeric * unit_price) * ((1)::numeric - (discount / (100)::numeric))),
    CONSTRAINT accounting_invoice_items_pkey PRIMARY KEY (id)
);

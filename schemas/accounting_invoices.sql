-- PostgreSQL

CREATE TABLE accounting_invoices (
    id                                 bigint NOT NULL DEFAULT nextval('accounting_invoices_id_seq'::regclass),
    uuid                               uuid DEFAULT gen_random_uuid(),
    accounting_account_id              bigint NOT NULL,
    invoice_number                     text,
    exchange_rate                      json,
    amount                             numeric(10,4) NOT NULL,
    common_currency_id                 bigint NOT NULL,
    vat                                numeric(10,4) NOT NULL,
    is_paid                            boolean DEFAULT false,
    is_refund                          boolean DEFAULT false,
    due_date                           timestamp with time zone,
    iam_account_id                     bigint NOT NULL,
    iam_user_id                        bigint NOT NULL,
    is_payable                         boolean NOT NULL DEFAULT false,
    is_sealed                          boolean NOT NULL DEFAULT false,
    note                               text DEFAULT false,
    created_at                         timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                         timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                         timestamp with time zone,
    term_year                          integer,
    term_month                         integer,
    is_cancelled                       boolean DEFAULT false,
    cancellation_reason                text,
    payment_link_url                   text,
    is_commission_invoice              boolean DEFAULT false, -- Indicates if the invoice is a commission invoice
    distributor_commission_invoice_id  bigint, -- [alias:accounting_invoices_id]
    integrator_commission_invoice_id   bigint, -- [alias:accounting_invoices_id]
    reseller_commission_invoice_id     bigint, -- [alias:accounting_invoices_id]
    affiliate_commission_invoice_id    bigint, -- [alias:accounting_invoices_id]
    CONSTRAINT accounting_invoices_pkey PRIMARY KEY (id)
);

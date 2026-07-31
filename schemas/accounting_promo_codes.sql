-- PostgreSQL

CREATE TABLE accounting_promo_codes (
    id                  bigint NOT NULL DEFAULT nextval('accounting_promo_codes_id_seq'::regclass),
    uuid                uuid DEFAULT gen_random_uuid(),
    code                text NOT NULL,
    iam_account_id      bigint,
    iam_user_id         bigint,
    value               integer NOT NULL,
    common_currency_id  bigint NOT NULL,
    gift_code_data      text,
    created_at          timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at          timestamp with time zone,
    CONSTRAINT accounting_promo_codes_pkey PRIMARY KEY (id)
);

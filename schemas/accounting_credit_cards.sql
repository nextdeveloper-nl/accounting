-- PostgreSQL

CREATE TABLE accounting_credit_cards (
    id                bigint NOT NULL DEFAULT nextval('accounting_credit_cards_id_seq'::regclass),
    uuid              uuid DEFAULT gen_random_uuid(),
    name              text,
    type              text,
    cc_holder_name    text NOT NULL,
    cc_number         text NOT NULL,
    cc_month          text NOT NULL,
    cc_year           text NOT NULL,
    cc_cvv            text NOT NULL,
    is_default        boolean DEFAULT false,
    is_valid          boolean DEFAULT false,
    is_active         boolean DEFAULT false,
    is_3d_secure      boolean DEFAULT false,
    iam_account_id    bigint NOT NULL,
    iam_user_id       bigint NOT NULL,
    created_at        timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at        timestamp with time zone,
    pg_card_user_key  text, -- User key returned by the payment gateway for the stored card
    pg_card_token     text, -- Token returned by the payment gateway representing the stored card
    pg_provider       text, -- Payment gateway provider for the stored card
    is_stored_at_pg   boolean NOT NULL DEFAULT false, -- Indicates whether the card information is stored at the payment gateway. If true, the system should use pg_card_user_key and pg_card_token for transactions instead of storing sensitive card details locally.
    CONSTRAINT accounting_credit_cards_pkey PRIMARY KEY (id)
);

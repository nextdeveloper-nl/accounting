-- PostgreSQL

CREATE TABLE accounting_credit_transactions (
    id                     bigint NOT NULL DEFAULT nextval('accounting_credit_transactions_id_seq'::regclass),
    uuid                   uuid DEFAULT gen_random_uuid(),
    accounting_account_id  bigint NOT NULL,
    amount                 numeric(20,6) NOT NULL, -- Positive for topup/refund, negative for deduction/expiry.
    type                   text NOT NULL, -- Movement type: topup, deduction, refund, expiry.
    balance_after          numeric(20,6) NOT NULL, -- Snapshot of accounting_accounts.credit after this transaction.
    object_type            text, -- The object that triggered this transaction, e.g. NextDeveloper\AI\Runs.
    object_id              bigint, -- The id of the object that triggered this transaction.
    description            text,
    iam_account_id         bigint NOT NULL,
    iam_user_id            bigint,
    created_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at             timestamp with time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at             timestamp with time zone,
    CONSTRAINT accounting_credit_transactions_accounting_account_id_fkey FOREIGN KEY (accounting_account_id) REFERENCES accounting_accounts(id) ON DELETE CASCADE,
    CONSTRAINT accounting_credit_transactions_pkey PRIMARY KEY (id)
);

CREATE INDEX idx_accounting_credit_transactions_account ON public.accounting_credit_transactions USING btree (accounting_account_id);
CREATE INDEX idx_accounting_credit_transactions_iam_account ON public.accounting_credit_transactions USING btree (iam_account_id);
CREATE INDEX idx_accounting_credit_transactions_object ON public.accounting_credit_transactions USING btree (object_type, object_id);
CREATE INDEX idx_accounting_credit_transactions_type ON public.accounting_credit_transactions USING btree (type);

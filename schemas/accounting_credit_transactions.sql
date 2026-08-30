CREATE TABLE accounting_credit_transactions (
    id BIGSERIAL PRIMARY KEY,
    uuid UUID NOT NULL DEFAULT gen_random_uuid(),
    accounting_account_id BIGINT NOT NULL,
    amount NUMERIC(12,6) NOT NULL,
    type TEXT NOT NULL,
    balance_after NUMERIC(12,6) NOT NULL,
    object_type TEXT,
    object_id BIGINT,
    description TEXT,
    iam_account_id BIGINT NOT NULL,
    iam_user_id BIGINT,
    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMPTZ
);

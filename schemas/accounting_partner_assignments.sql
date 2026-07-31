-- PostgreSQL
-- [label:Logs changes to partners associated with accounting accounts]

CREATE TABLE accounting_partner_assignments (
    id                     bigint NOT NULL DEFAULT nextval('accounting_partner_assignments_id_seq'::regclass),
    uuid                   uuid NOT NULL DEFAULT gen_random_uuid(),
    accounting_account_id  bigint NOT NULL,
    type                   text NOT NULL, -- [label:Type of partner: distributor, integrator, reseller]
    old_partner_id         bigint, -- [alias:accounting_account_id][label:ID of old partner]
    new_partner_id         bigint, -- [alias:accounting_account_id][label:ID of the new partner]
    started_at             timestamp with time zone NOT NULL DEFAULT now(), -- [label:Timestamp when the partner assignment started]
    finished_at            timestamp without time zone, -- [label:Timestamp when the partner assignment finished]
    iam_user_id            bigint, -- [label:ID of the IAM user who made the change]
    iam_account_id         bigint, -- [label:ID of the IAM account associated with the change]
    reason                 text, -- [label:Reason for the partner change]
    created_at             timestamp with time zone NOT NULL DEFAULT now(),
    updated_at             timestamp with time zone NOT NULL DEFAULT now(),
    deleted_at             timestamp without time zone,
    CONSTRAINT accounting_partner_assignments_pkey PRIMARY KEY (id),
    CONSTRAINT accounting_partner_assignments_uuid_key UNIQUE (uuid)
);

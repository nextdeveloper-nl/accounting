-- PostgreSQL
-- Table to manage partnerships with external agencies or brands

CREATE TABLE accounting_partnerships (
    id                         bigint NOT NULL DEFAULT nextval('accounting_partnerships_id_seq'::regclass),
    uuid                       uuid NOT NULL DEFAULT gen_random_uuid(),
    iam_account_id             bigint NOT NULL, -- The account that owns the partnership
    partner_code               text, -- Unique code for the partnership
    is_brand_ambassador        boolean DEFAULT false, -- Indicates if this partnership is a brand ambassador
    customer_count             integer DEFAULT 0, -- Number of customers associated with this partnership
    level                      integer DEFAULT 1, -- Level of the partnership (e.g. 1, 2, 3)
    reward_points              integer DEFAULT 0, -- Total reward points accumulated by the partnership
    boosts                     json, -- JSON object containing boost configurations for the partnership
    mystery_box                json, -- JSON object containing mystery box configurations for the partnership
    badges                     json, -- JSON object containing badge configurations for the partnership
    is_approved                boolean DEFAULT false, -- Indicates if the partnership is approved
    technical_capabilities     text[], -- List of technical capabilities of the partnership (e.g. software development, integration, email marketing)
    industry                   text, -- Industry of the partnership
    sector_focus               text[], -- List of sectors the partnership focuses on
    special_interest           text[], -- List of special interests of the partnership
    compliance_certifications  text[], -- List of compliance certifications held by the partnership
    target_group               text[], -- List of target groups for the partnership
    meeting_link               text, -- Link for scheduling meetings with the partnership
    created_at                 timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at                 timestamp without time zone NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at                 timestamp without time zone,
    accounting_account_id      bigint,
    operating_countries        text[],
    operating_cities           text[],
    CONSTRAINT accounting_partnerships_pkey PRIMARY KEY (id)
);

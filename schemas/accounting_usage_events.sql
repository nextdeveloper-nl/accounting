-- PostgreSQL
--
-- accounting_usage_events
--
-- Append-only usage log. One row = one resource, one meter, one state segment
-- (or one metered delta such as S3 egress). A segment is a period during which the
-- resource stayed in the same state and size, cut at hour boundaries so a row never
-- spans two hours. A VM that runs untouched for an hour is one row; a VM halted from
-- 14:23 to 14:32 is three rows (running, halted with quantity 0, running). The halted
-- row is the evidence shown to the customer that the period was not billed.
-- Rows are never edited: corrections are new
-- rows with kind = 'adjustment' that point at the row they adjust. Only `status`
-- moves (unrated -> rated | void) when the rating job picks the row up.
--
-- Rows are unpriced on purpose. Pricing lives in a separate charges table so the
-- usage record stays a pure statement of what happened.
--
-- Partitioned monthly on period_start. A scheduled job must create the next
-- month's partition before it starts, otherwise inserts for that month fail.
-- This file is applied manually (no Laravel migration).

CREATE TABLE accounting_usage_events (
    id BIGSERIAL,
    uuid UUID NOT NULL DEFAULT gen_random_uuid(),
    iam_account_id BIGINT NOT NULL REFERENCES iam_accounts(id),
    iam_user_id BIGINT REFERENCES iam_users(id),
    accounting_account_id BIGINT NOT NULL,

    -- What was consumed, in the meter's unit (e.g. vm.ram_gb_second, disk.gb_second, ip.second)
    meter TEXT NOT NULL,
    quantity NUMERIC(20,6) NOT NULL,
    unit TEXT NOT NULL,
    -- Segment bounds. Hourly rating groups rows by date_trunc('hour', period_start).
    period_start TIMESTAMPTZ NOT NULL,
    period_end TIMESTAMPTZ NOT NULL,

    -- State of the resource during the segment (running | halted | ...). Lets the
    -- customer report show why a segment has quantity 0 without inferring it.
    resource_state TEXT,

    -- Which resource consumed it. object_type is the full model class, no FK so
    -- history survives resource deletion.
    object_type TEXT NOT NULL,
    object_id BIGINT NOT NULL,

    -- Attribution: disk/IP -> VM, VM -> autoscaling group or project
    parent_object_type TEXT,
    parent_object_id BIGINT,

    -- Pool (compute/storage/network) that set the price
    pool_object_type TEXT,
    pool_object_id BIGINT,

    -- Name snapshot so reports stay readable after the resource is deleted
    resource_label TEXT,

    -- state_timeline | agent | scheduler | reconciler
    source TEXT NOT NULL,
    -- usage | adjustment
    kind TEXT NOT NULL DEFAULT 'usage',
    adjusts_usage_event_id BIGINT,

    -- account:meter:object:segment_start key; makes retries safe
    idempotency_key TEXT NOT NULL,

    -- unrated | rated | void
    status TEXT NOT NULL DEFAULT 'unrated',
    metadata JSON,

    created_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMPTZ NOT NULL DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMPTZ,

    -- Primary and unique keys must include the partition key
    PRIMARY KEY (id, period_start),
    UNIQUE (idempotency_key, period_start)
) PARTITION BY RANGE (period_start);

-- Statements and runway queries: everything for an account in a window
CREATE INDEX accounting_usage_events_account_period_idx
    ON accounting_usage_events (iam_account_id, period_start);

-- Per-resource cost views and parent/child roll-ups
CREATE INDEX accounting_usage_events_object_period_idx
    ON accounting_usage_events (object_type, object_id, period_start);

-- Rating job picks up unrated rows
CREATE INDEX accounting_usage_events_unrated_idx
    ON accounting_usage_events (period_start)
    WHERE status = 'unrated';

-- Initial monthly partitions. Times are stored as local wall-clock time of the application
-- timezone (Europe/Istanbul), the project convention, so the bounds below are local months
-- (the stored value 2026-10-01 00:00 is the first hour of local October).
CREATE TABLE accounting_usage_events_2026_10 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2026-10-01 00:00:00+00') TO ('2026-11-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2026_11 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2026-11-01 00:00:00+00') TO ('2026-12-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2026_12 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2026-12-01 00:00:00+00') TO ('2027-01-01 00:00:00+00');

-- 2027, so the first year of production needs no manual partition work. A partition for each
-- following month must exist BEFORE its first hour (inserts for a month without one fail).
CREATE TABLE accounting_usage_events_2027_01 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-01-01 00:00:00+00') TO ('2027-02-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_02 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-02-01 00:00:00+00') TO ('2027-03-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_03 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-03-01 00:00:00+00') TO ('2027-04-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_04 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-04-01 00:00:00+00') TO ('2027-05-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_05 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-05-01 00:00:00+00') TO ('2027-06-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_06 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-06-01 00:00:00+00') TO ('2027-07-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_07 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-07-01 00:00:00+00') TO ('2027-08-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_08 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-08-01 00:00:00+00') TO ('2027-09-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_09 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-09-01 00:00:00+00') TO ('2027-10-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_10 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-10-01 00:00:00+00') TO ('2027-11-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_11 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-11-01 00:00:00+00') TO ('2027-12-01 00:00:00+00');
CREATE TABLE accounting_usage_events_2027_12 PARTITION OF accounting_usage_events
    FOR VALUES FROM ('2027-12-01 00:00:00+00') TO ('2028-01-01 00:00:00+00');

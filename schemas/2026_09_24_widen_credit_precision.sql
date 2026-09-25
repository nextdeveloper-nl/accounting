-- PostgreSQL
-- Widen accounting_accounts.credit / balance from numeric(10,2) to numeric(18,6).
--
-- Why: usage charges (e.g. AI runs at $0.00065) are debited per use. With 2 decimals
-- Postgres rounds `credit - 0.00065` back to the same cents, so small charges were never
-- deducted and $0.005–$0.01 charges became a full cent. accounting_credit_transactions.amount
-- is already numeric(20,6); this makes the running balance match the ledger.
--
-- Postgres refuses to change a column type while views depend on it, so every view that
-- (transitively) depends on accounting_accounts is saved, dropped, and recreated from its own
-- definition in the same transaction. DROP VIEW is run without CASCADE: if an unexpected
-- dependent exists the whole script aborts and nothing changes.
--
-- Idempotent: re-running only recreates the views.

BEGIN;

CREATE TEMP TABLE _accounting_saved_views ON COMMIT DROP AS
WITH RECURSIVE deps AS (
    SELECT DISTINCT r.ev_class AS oid, 1 AS depth
    FROM pg_depend d
    JOIN pg_rewrite r ON r.oid = d.objid
    WHERE d.refobjid = 'accounting_accounts'::regclass
      AND r.ev_class <> 'accounting_accounts'::regclass
    UNION
    SELECT DISTINCT r.ev_class, deps.depth + 1
    FROM deps
    JOIN pg_depend d ON d.refobjid = deps.oid
    JOIN pg_rewrite r ON r.oid = d.objid
    WHERE r.ev_class <> deps.oid
)
SELECT n.nspname                        AS schema_name,
       c.relname                        AS view_name,
       c.relkind                        AS kind,
       pg_get_viewdef(c.oid, true)      AS definition,
       pg_get_userbyid(c.relowner)      AS owner,
       c.reloptions                     AS options,
       obj_description(c.oid, 'pg_class') AS comment,
       MAX(deps.depth)                  AS depth
FROM deps
JOIN pg_class c ON c.oid = deps.oid
JOIN pg_namespace n ON n.oid = c.relnamespace
GROUP BY 1, 2, 3, 4, 5, 6, 7;

DO $$
DECLARE
    saved record;
    view_count integer;
BEGIN
    SELECT count(*) INTO view_count FROM _accounting_saved_views WHERE kind <> 'v';
    IF view_count > 0 THEN
        RAISE EXCEPTION 'Materialized views depend on accounting_accounts; handle them manually.';
    END IF;

    FOR saved IN SELECT * FROM _accounting_saved_views ORDER BY depth DESC, view_name LOOP
        EXECUTE format('DROP VIEW %I.%I', saved.schema_name, saved.view_name);
    END LOOP;

    ALTER TABLE accounting_accounts
        ALTER COLUMN credit  TYPE numeric(18, 6),
        ALTER COLUMN balance TYPE numeric(18, 6);

    FOR saved IN SELECT * FROM _accounting_saved_views ORDER BY depth ASC, view_name LOOP
        EXECUTE format(
            'CREATE VIEW %I.%I %s AS %s',
            saved.schema_name,
            saved.view_name,
            CASE WHEN saved.options IS NULL THEN '' ELSE 'WITH (' || array_to_string(saved.options, ', ') || ')' END,
            saved.definition
        );
        EXECUTE format('ALTER VIEW %I.%I OWNER TO %I', saved.schema_name, saved.view_name, saved.owner);

        IF saved.comment IS NOT NULL THEN
            EXECUTE format('COMMENT ON VIEW %I.%I IS %L', saved.schema_name, saved.view_name, saved.comment);
        END IF;
    END LOOP;

    RAISE NOTICE 'Widened credit/balance and recreated % views.', (SELECT count(*) FROM _accounting_saved_views);
END $$;

COMMIT;

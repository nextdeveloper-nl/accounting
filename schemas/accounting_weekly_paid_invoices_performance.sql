-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)
-- Weekly sum of paid invoices for the last 52 weeks (1 year), showing 0 if no data

CREATE OR REPLACE VIEW accounting_weekly_paid_invoices_performance AS
WITH week_series AS (
         SELECT date_trunc('week'::text, generate_series(CURRENT_DATE - '1 year'::interval, CURRENT_DATE::timestamp without time zone, '7 days'::interval))::date AS week_start
        )
 SELECT ws.week_start,
    ws.week_start + '6 days'::interval AS week_end,
    to_char(ws.week_start::timestamp with time zone, 'IYYY-IW'::text) AS week_number,
    COALESCE(count(ai.id), 0::bigint) AS count,
    COALESCE(sum(ai.amount), 0::numeric) AS total_amount
   FROM week_series ws
     LEFT JOIN accounting_invoices ai ON date_trunc('week'::text, ai.created_at::date::timestamp with time zone) = ws.week_start AND ai.is_paid = true AND ai.created_at >= (CURRENT_DATE - '1 year'::interval) AND ai.deleted_at IS NULL
  GROUP BY ws.week_start
  ORDER BY ws.week_start DESC;

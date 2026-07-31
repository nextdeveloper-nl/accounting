-- PostgreSQL
-- VIEW (read-only; re-run this file with CREATE OR REPLACE VIEW whenever the SELECT needs to change)
-- Monthly statistics for paid invoices for the last 12 months (1 year): count, sum, averages, min/max amounts, showing 0 if no data

CREATE OR REPLACE VIEW accounting_monthly_paid_invoices_performance AS
WITH month_series AS (
         SELECT date_trunc('month'::text, generate_series(date_trunc('month'::text, CURRENT_DATE - '11 mons'::interval), date_trunc('month'::text, CURRENT_DATE::timestamp with time zone)::timestamp without time zone, '1 mon'::interval))::date AS month_start
        )
 SELECT ms.month_start,
    (ms.month_start + '1 mon'::interval - '1 day'::interval)::date AS month_end,
    to_char(ms.month_start::timestamp with time zone, 'Month YYYY'::text) AS month_name,
    to_char(ms.month_start::timestamp with time zone, 'YYYY-MM'::text) AS month_code,
    ai.common_currency_id,
    COALESCE(count(ai.id), 0::bigint) AS count,
    COALESCE(sum(ai.amount), 0::numeric) AS total_amount,
        CASE
            WHEN count(ai.id) > 0 THEN COALESCE(avg(ai.amount), 0::numeric)
            ELSE 0::numeric
        END AS avg_amount,
        CASE
            WHEN count(ai.id) > 0 THEN COALESCE(min(ai.amount), 0::numeric)
            ELSE 0::numeric
        END AS min_amount,
        CASE
            WHEN count(ai.id) > 0 THEN COALESCE(max(ai.amount), 0::numeric)
            ELSE 0::numeric
        END AS max_amount
   FROM month_series ms
     LEFT JOIN accounting_invoices ai ON date_trunc('month'::text, ai.created_at) = ms.month_start AND ai.is_paid = true AND ai.deleted_at IS NULL
  GROUP BY ms.month_start, ai.common_currency_id
  ORDER BY ms.month_start DESC, ai.common_currency_id;

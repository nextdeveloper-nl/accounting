<?php

namespace NextDeveloper\Accounting\Services;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use NextDeveloper\Accounting\Database\Models\UsageEvents;

/**
 * Writes rows to the append-only accounting_usage_events table.
 *
 * Every write goes through record(), which is idempotent on (idempotency_key, period_start),
 * so an emitter job can be retried or re-run for the same hour without double-writing usage.
 * Existing rows are never updated here: usage is immutable, corrections are new rows with
 * kind = 'adjustment'.
 *
 * @package NextDeveloper\Accounting\Services
 */
class UsageEventsService
{
    /**
     * Records one usage event unless one with the same idempotency key already exists.
     *
     * @param array $data Column values; iam_account_id, meter, quantity, unit, period_start,
     *                    period_end, object_type, object_id, source and idempotency_key are required.
     *
     * @return UsageEvents The new row, or the already stored row when this was a repeat
     *                     (check wasRecentlyCreated on the result to tell them apart).
     */
    public static function record(array $data): UsageEvents
    {
        $data = self::normalizeTimes($data);

        $existing = self::find($data['idempotency_key'], $data['period_start']);

        if ($existing) {
            return $existing;
        }

        try {
            return UsageEvents::create($data);
        } catch (QueryException $e) {
            // 23505 = unique_violation: another worker wrote the same key between our check and
            // our insert, which is the exact case idempotency has to absorb. Return its row.
            if ($e->getCode() === '23505') {
                $existing = self::find($data['idempotency_key'], $data['period_start']);

                if ($existing) {
                    return $existing;
                }
            }

            throw $e;
        }
    }

    /**
     * Bulk version of record() for emitters that write thousands of rows per hour.
     *
     * One INSERT ... ON CONFLICT DO NOTHING per chunk instead of a lookup and an insert per row.
     * The unique key (idempotency_key, period_start) makes Postgres skip rows that already
     * exist, so re-running an hour stays safe and nothing stored is ever overwritten.
     *
     * @param array<int, array> $rows Same column shape as record().
     *
     * @return int Number of rows actually inserted (rows that already existed are not counted).
     */
    public static function recordMany(array $rows): int
    {
        if (!$rows) {
            return 0;
        }

        $columns = [
            'iam_account_id', 'iam_user_id', 'accounting_account_id', 'meter', 'quantity', 'unit',
            'period_start', 'period_end', 'resource_state', 'object_type', 'object_id',
            'parent_object_type', 'parent_object_id', 'pool_object_type', 'pool_object_id',
            'resource_label', 'source', 'kind', 'adjusts_usage_event_id', 'idempotency_key',
            'status', 'metadata',
        ];

        $inserted = 0;

        foreach (array_chunk($rows, 500) as $chunk) {
            // A bulk insert needs every row to carry the same columns, so missing ones become null
            // (the table defaults for kind and status are applied by filling them here).
            $prepared = array_map(function ($row) use ($columns) {
                $row = self::normalizeTimes($row);
                $values = [];

                foreach ($columns as $column) {
                    $values[$column] = $row[$column] ?? null;
                }

                $values['kind'] = $values['kind'] ?? 'usage';
                $values['status'] = $values['status'] ?? 'unrated';
                $values['metadata'] = $values['metadata'] === null ? null : json_encode($values['metadata']);

                return $values;
            }, $chunk);

            $inserted += UsageEvents::insertOrIgnore($prepared);
        }

        return $inserted;
    }

    /**
     * Puts period_start and period_end in the application timezone before they are written or
     * looked up.
     *
     * Project convention (decided with the team): timestamps are stored as the local wall-clock
     * time of the application timezone (Europe/Istanbul, +03). Laravel binds a Carbon as a plain
     * "Y-m-d H:i:s" string in the Carbon's own timezone, so every value must be converted to the app
     * timezone first, otherwise values that arrive in UTC (for example the created_at of stats rows
     * read from the database) would be stored as a different wall time than the rest.
     * Partitions therefore follow local months: 2026-10-01 00:00 local is stored as 2026-10-01 00:00.
     */
    private static function normalizeTimes(array $data): array
    {
        foreach (['period_start', 'period_end'] as $column) {
            if (isset($data[$column])) {
                $data[$column] = Carbon::parse($data[$column])->setTimezone(config('app.timezone'));
            }
        }

        return $data;
    }

    /**
     * Finds a stored event by its idempotency key. period_start is part of the lookup because
     * the table is partitioned on it and the unique key is (idempotency_key, period_start).
     */
    public static function find(string $idempotencyKey, $periodStart): ?UsageEvents
    {
        return UsageEvents::where('idempotency_key', $idempotencyKey)
            ->where('period_start', Carbon::parse($periodStart)->setTimezone(config('app.timezone')))
            ->first();
    }
}

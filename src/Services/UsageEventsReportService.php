<?php

namespace NextDeveloper\Accounting\Services;

use Carbon\Carbon;
use NextDeveloper\Accounting\Database\Models\UsageEvents;

/**
 * Builds the monthly report of accounting_usage_events: every resource with its hourly series, plus
 * the signals that show whether the emitters work (hours with no row, overlapping rows, partial
 * hours, how many resources reported in each hour).
 *
 * Read-only. The result is a plain array so the same data can feed the HTML report, a JSON export
 * and, later, a panel API endpoint that draws the hourly graph.
 *
 * Time convention: usage events store local wall-clock time of the application timezone, labelled
 * +00 by the database. The hour index is therefore read from the stored text itself and never
 * converted: index 0 is the first hour of the local month.
 */
class UsageEventsReportService
{
    /**
     * Per meter: a readable name, the unit shown on the graph, the divisor that turns the stored
     * quantity of one hour into that unit, and whether the rows are time segments (so seconds covered
     * can be checked against the 3600 of an hour). Unknown meters are shown as raw quantity.
     */
    private const METERS = [
        'vm.ram_gb_second'         => ['name' => 'VM RAM',           'unit' => 'GB RAM (hourly average)',  'divisor' => 3600, 'segments' => true],
        'disk.gb_second'           => ['name' => 'Disk',             'unit' => 'GB (hourly average)',      'divisor' => 3600, 'segments' => true],
        'ip.second'                => ['name' => 'IP address',       'unit' => 'present (share of hour)',  'divisor' => 3600, 'segments' => true],
        'backup.storage_gb_second' => ['name' => 'Backup',           'unit' => 'GB (hourly average)',      'divisor' => 3600, 'segments' => true],
        's3.storage_gb_second'     => ['name' => 'S3 storage',       'unit' => 'GB (hourly average)',      'divisor' => 3600, 'segments' => true],
        's3.egress_gb'             => ['name' => 'S3 egress',        'unit' => 'GB in the hour',           'divisor' => 1,    'segments' => false],
        's3.ingress_gb'            => ['name' => 'S3 ingress',       'unit' => 'GB in the hour',           'divisor' => 1,    'segments' => false],
        'monitoring.check_second'  => ['name' => 'Monitoring checks', 'unit' => 'billable checks (average)', 'divisor' => 3600, 'segments' => false],
        'crm.pusher_usage'         => ['name' => 'CRM pusher usage', 'unit' => 'weighted pushes',          'divisor' => 1,    'segments' => false],
        'autoquill.subscription'   => ['name' => 'AutoQuill',        'unit' => 'events',                   'divisor' => 1,    'segments' => false],
    ];

    /** Most hour numbers listed per issue, so one broken resource can not bloat the report. */
    private const ISSUE_HOURS_CAP = 24;

    /**
     * @param Carbon   $month        Any moment inside the wanted month (application timezone).
     * @param int|null $iamAccountId Restrict to one IAM account, null for all.
     *
     * @return array<string, mixed>
     */
    public function monthly(Carbon $month, ?int $iamAccountId = null): array
    {
        $tz = config('app.timezone');
        $start = $month->copy()->setTimezone($tz)->startOfMonth();
        $end = $start->copy()->addMonth();
        $hoursInMonth = (int) ($start->daysInMonth * 24);

        $resources = [];   // key => accumulated resource
        $rowCount = 0;
        $lastHour = -1;

        $query = UsageEvents::query()->toBase()
            ->select(['id', 'meter', 'kind', 'quantity', 'period_start', 'period_end', 'object_type', 'object_id',
                'resource_label', 'resource_state', 'iam_account_id', 'parent_object_type', 'parent_object_id'])
            ->whereNull('deleted_at')
            ->where('period_start', '>=', $start->format('Y-m-d H:i:s'))
            ->where('period_start', '<', $end->format('Y-m-d H:i:s'));

        if ($iamAccountId) {
            $query->where('iam_account_id', $iamAccountId);
        }

        $query->chunkById(20000, function ($rows) use (&$resources, &$rowCount, &$lastHour, $hoursInMonth) {
            foreach ($rows as $row) {
                $rowCount++;
                $this->addRow($resources, $row, $hoursInMonth, $lastHour);
            }
        }, 'id');

        return $this->summarize($resources, $start, $hoursInMonth, $rowCount, $lastHour, $iamAccountId);
    }

    /**
     * Adds one usage event row (an object with the columns selected in monthly()) to the resource
     * accumulator. Public so the report logic can be tested with synthetic rows.
     *
     * @param array<string, array> $resources
     */
    public function addRow(array &$resources, object $row, int $hoursInMonth, int &$lastHour): void
    {
        // Hour index inside the month, read straight from the stored text "YYYY-MM-DD HH:..".
        $index = ((int) substr($row->period_start, 8, 2) - 1) * 24 + (int) substr($row->period_start, 11, 2);

        if ($index < 0 || $index >= $hoursInMonth) {
            return;
        }

        $key = $row->meter . '|' . $row->object_type . '|' . $row->object_id;

        if (!isset($resources[$key])) {
            $resources[$key] = [
                'meter'     => $row->meter,
                'type'      => class_basename($row->object_type),
                'object_id' => (int) $row->object_id,
                'label'     => $row->resource_label,
                'account'   => (int) $row->iam_account_id,
                'parent'    => $row->parent_object_id ? class_basename($row->parent_object_type) . ' #' . $row->parent_object_id : null,
                'qty'       => [],     // hour index => stored quantity
                'rows'      => [],     // hour index => number of rows
                'seconds'   => [],     // hour index => seconds covered by the rows (segment meters)
                'states'    => [],     // hour index => last resource_state
                'adjustments' => 0,
            ];
        }

        $r = &$resources[$key];
        $r['qty'][$index] = ($r['qty'][$index] ?? 0) + (float) $row->quantity;
        $r['rows'][$index] = ($r['rows'][$index] ?? 0) + 1;
        $r['states'][$index] = $row->resource_state;

        if ($row->kind === 'adjustment') {
            $r['adjustments']++;
        } elseif ((self::METERS[$row->meter]['segments'] ?? false)) {
            $r['seconds'][$index] = ($r['seconds'][$index] ?? 0)
                + (strtotime(substr($row->period_end, 0, 19) . ' UTC') - strtotime(substr($row->period_start, 0, 19) . ' UTC'));
        }

        if ($index > $lastHour) {
            $lastHour = $index;
        }

        unset($r);
    }

    /**
     * Turns the accumulated resources into the report array (series, issues, per-meter coverage).
     *
     * @param array<string, array> $resources
     *
     * @return array<string, mixed>
     */
    public function summarize(array $resources, Carbon $start, int $hoursInMonth, int $rowCount, int $lastHour, ?int $iamAccountId = null): array
    {
        $tz = config('app.timezone');
        $length = $lastHour + 1;
        $output = [];
        $coverage = [];   // meter => [hour index => resources reporting]
        $meterStats = [];

        foreach ($resources as $key => $r) {
            $def = self::METERS[$r['meter']] ?? ['name' => $r['meter'], 'unit' => 'quantity', 'divisor' => 1, 'segments' => false];

            $series = array_fill(0, $length, null);
            $rowsPerHour = array_fill(0, $length, 0);
            $total = 0.0;

            foreach ($r['qty'] as $i => $qty) {
                $series[$i] = round($qty / $def['divisor'], 4);
                $rowsPerHour[$i] = $r['rows'][$i];
                $total += $qty;
                $coverage[$r['meter']][$i] = ($coverage[$r['meter']][$i] ?? 0) + 1;
            }

            $observed = array_keys($r['qty']);
            sort($observed);
            $first = $observed[0];
            $last = end($observed);

            // Issues: only for segment meters, which should have a row for every hour of their life.
            $gaps = $overlaps = $partials = [];

            if ($def['segments']) {
                for ($i = $first; $i <= $last; $i++) {
                    if (!isset($r['qty'][$i])) {
                        $gaps[] = $i;
                        continue;
                    }

                    $seconds = $r['seconds'][$i] ?? 0;

                    if ($seconds > 3601) {
                        $overlaps[] = $i;
                    } elseif ($seconds < 3599 && $i !== $first && $i !== $last) {
                        $partials[] = $i;
                    }
                }
            }

            $output[] = [
                'id'       => count($output),
                'meter'    => $r['meter'],
                'type'     => $r['type'],
                'object_id' => $r['object_id'],
                'label'    => $r['label'] ?: ($r['type'] . ' #' . $r['object_id']),
                'account'  => $r['account'],
                'parent'   => $r['parent'],
                'total'    => round($total, 4),
                'first'    => $first,
                'last'     => $last,
                'adjustments' => $r['adjustments'],
                'series'   => $series,
                'rows'     => $rowsPerHour,
                'issues'   => [
                    'gaps'     => array_slice($gaps, 0, self::ISSUE_HOURS_CAP),
                    'overlaps' => array_slice($overlaps, 0, self::ISSUE_HOURS_CAP),
                    'partials' => array_slice($partials, 0, self::ISSUE_HOURS_CAP),
                    'counts'   => ['gaps' => count($gaps), 'overlaps' => count($overlaps), 'partials' => count($partials)],
                ],
            ];

            $m = $r['meter'];
            $meterStats[$m]['resources'] = ($meterStats[$m]['resources'] ?? 0) + 1;
            $meterStats[$m]['rows'] = ($meterStats[$m]['rows'] ?? 0) + array_sum($r['rows']);
            $meterStats[$m]['total'] = ($meterStats[$m]['total'] ?? 0) + $total;
            $meterStats[$m]['issues'] = ($meterStats[$m]['issues'] ?? 0) + count($gaps) + count($overlaps) + count($partials);
        }

        $meters = [];
        foreach ($meterStats as $meter => $stats) {
            $def = self::METERS[$meter] ?? ['name' => $meter, 'unit' => 'quantity', 'divisor' => 1, 'segments' => false];
            $line = array_fill(0, $length, 0);
            foreach ($coverage[$meter] ?? [] as $i => $n) {
                $line[$i] = $n;
            }

            // Last hour in which any resource of this meter reported, and how many hours that is behind
            // the newest data of the whole month. A meter that stopped emitting shows up here.
            $lastReported = -1;
            foreach ($line as $i => $n) {
                if ($n > 0) {
                    $lastReported = $i;
                }
            }

            $meters[] = [
                'last_hour'   => $lastReported,
                'hours_behind' => $lastReported < 0 ? $length : ($length - 1 - $lastReported),
                'meter'     => $meter,
                'name'      => $def['name'],
                'unit'      => $def['unit'],
                'divisor'   => $def['divisor'],
                'segments'  => $def['segments'],
                'resources' => $stats['resources'],
                'rows'      => $stats['rows'],
                'total'     => round($stats['total'], 2),
                'issues'    => $stats['issues'],
                'coverage'  => $line,
            ];
        }

        usort($meters, fn($a, $b) => strcmp($a['meter'], $b['meter']));
        usort($output, fn($a, $b) => [$a['meter'], -$a['total']] <=> [$b['meter'], -$b['total']]);
        foreach ($output as $i => &$o) {
            $o['id'] = $i;
        }
        unset($o);

        return [
            'month'         => $start->format('Y-m'),
            'month_label'   => $start->format('F Y'),
            'month_start'   => $start->format('Y-m-d H:i:s'),
            'timezone'      => $tz,
            'hours_in_month' => $hoursInMonth,
            'hours_with_data' => $length,
            'rows'          => $rowCount,
            'generated_at'  => Carbon::now()->format('Y-m-d H:i'),
            'account_filter' => $iamAccountId,
            'meters'        => $meters,
            'resources'     => $output,
        ];
    }
}

<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * UsageEvents model.
 *
 * Append-only usage log, one row per resource, meter and state segment
 * (see schemas/accounting_usage_events.sql). The table is partitioned monthly on
 * period_start.
 *
 * This is an internal, write-only table for now (shadow mode, nothing reads it for billing),
 * so it deliberately has no observer and no authorization scopes. Rows are only created
 * through UsageEventsService. When a customer-facing read API is added, it must go through
 * a perspective model that carries AuthorizationScope instead of reading this model directly.
 *
 * @package NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $iam_account_id
 * @property integer $iam_user_id
 * @property integer $accounting_account_id
 * @property string $meter
 * @property string $quantity
 * @property string $unit
 * @property \Carbon\Carbon $period_start
 * @property \Carbon\Carbon $period_end
 * @property string $resource_state
 * @property string $object_type
 * @property integer $object_id
 * @property string $parent_object_type
 * @property integer $parent_object_id
 * @property string $pool_object_type
 * @property integer $pool_object_id
 * @property string $resource_label
 * @property string $source
 * @property string $kind
 * @property integer $adjusts_usage_event_id
 * @property string $idempotency_key
 * @property string $status
 * @property array $metadata
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class UsageEvents extends Model
{
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_usage_events';

    protected $guarded = [];

    protected $casts = [
        'id'                     => 'integer',
        'iam_account_id'         => 'integer',
        'iam_user_id'            => 'integer',
        'accounting_account_id'  => 'integer',
        'object_id'              => 'integer',
        'parent_object_id'       => 'integer',
        'pool_object_id'         => 'integer',
        'adjusts_usage_event_id' => 'integer',
        'period_start'           => 'datetime',
        'period_end'             => 'datetime',
        'metadata'               => 'array',
        'created_at'             => 'datetime',
        'updated_at'             => 'datetime',
        'deleted_at'             => 'datetime',
    ];
}

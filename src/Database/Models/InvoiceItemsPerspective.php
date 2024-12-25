<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\InvoiceItemsPerspectiveObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;

/**
 * InvoiceItemsPerspective model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $accounting_invoice_id
 * @property integer $term_year
 * @property integer $term_month
 * @property $invoice_amount
 * @property string $object_type
 * @property integer $object_id
 * @property $unit_price
 * @property integer $quantity
 * @property $total_price
 * @property integer $iam_account_id
 * @property string $name
 * @property integer $iam_user_id
 * @property string $accounting_identifier
 * @property $credit
 * @property integer $common_currency_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class InvoiceItemsPerspective extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_invoice_items_perspective';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'accounting_invoice_id',
            'term_year',
            'term_month',
            'invoice_amount',
            'object_type',
            'object_id',
            'unit_price',
            'quantity',
            'total_price',
            'iam_account_id',
            'name',
            'iam_user_id',
            'accounting_identifier',
            'credit',
            'common_currency_id',
    ];

    /**
      Here we have the fulltext fields. We can use these for fulltext search if enabled.
     */
    protected $fullTextFields = [

    ];

    /**
     @var array
     */
    protected $appends = [

    ];

    /**
     We are casting fields to objects so that we can work on them better
     *
     @var array
     */
    protected $casts = [
    'id' => 'integer',
    'accounting_invoice_id' => 'integer',
    'term_year' => 'integer',
    'term_month' => 'integer',
    'object_type' => 'string',
    'object_id' => 'integer',
    'quantity' => 'integer',
    'name' => 'string',
    'accounting_identifier' => 'string',
    'common_currency_id' => 'integer',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'created_at',
    'updated_at',
    'deleted_at',
    ];

    /**
     @var array
     */
    protected $with = [

    ];

    /**
     @var int
     */
    protected $perPage = 20;

    /**
     @return void
     */
    public static function boot()
    {
        parent::boot();

        //  We create and add Observer even if we wont use it.
        parent::observe(InvoiceItemsPerspectiveObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_invoice_items_perspective');

        if(!$modelScopes) { $modelScopes = [];
        }
        if (!$globalScopes) { $globalScopes = [];
        }

        $scopes = array_merge(
            $globalScopes,
            $modelScopes
        );

        if($scopes) {
            foreach ($scopes as $scope) {
                static::addGlobalScope(app($scope));
            }
        }
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

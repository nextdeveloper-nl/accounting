<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Commons\Database\Traits\HasStates;
use NextDeveloper\Accounting\Database\Observers\InvoicesObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;

/**
 * Invoices model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $accounting_account_id
 * @property string $invoice_number
 * @property $exchange_rate
 * @property $amount
 * @property integer $common_currency_id
 * @property $vat
 * @property boolean $is_paid
 * @property boolean $is_refund
 * @property \Carbon\Carbon $due_date
 * @property integer $iam_account_id
 * @property integer $iam_user_id
 * @property boolean $is_payable
 * @property boolean $is_sealed
 * @property string $note
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property integer $term_year
 * @property integer $term_month
 */
class Invoices extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_invoices';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'accounting_account_id',
            'invoice_number',
            'exchange_rate',
            'amount',
            'common_currency_id',
            'vat',
            'is_paid',
            'is_refund',
            'due_date',
            'iam_account_id',
            'iam_user_id',
            'is_payable',
            'is_sealed',
            'note',
            'term_year',
            'term_month',
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
    'accounting_account_id' => 'integer',
    'invoice_number' => 'string',
    'exchange_rate' => 'array',
    'common_currency_id' => 'integer',
    'is_paid' => 'boolean',
    'is_refund' => 'boolean',
    'due_date' => 'datetime',
    'is_payable' => 'boolean',
    'is_sealed' => 'boolean',
    'note' => 'string',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    'term_year' => 'integer',
    'term_month' => 'integer',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'due_date',
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
        parent::observe(InvoicesObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_invoices');

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

    public function accounts() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\Accounting\Database\Models\Accounts::class);
    }
    
    public function users() : \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(\NextDeveloper\IAM\Database\Models\Users::class);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE












}

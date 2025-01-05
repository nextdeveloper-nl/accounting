<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\InvoicesPerspectiveObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;

/**
 * InvoicesPerspective model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $term_year
 * @property integer $term_month
 * @property $amount
 * @property boolean $is_paid
 * @property boolean $is_payable
 * @property boolean $is_refund
 * @property boolean $is_sealed
 * @property string $name
 * @property integer $common_country_id
 * @property integer $common_domain_id
 * @property integer $iam_account_id
 * @property integer $iam_user_id
 * @property integer $iam_account_type_id
 * @property string $accounting_identifier
 * @property $credit
 * @property integer $common_currency_id
 * @property integer $accounting_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class InvoicesPerspective extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_invoices_perspective';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'term_year',
            'term_month',
            'amount',
            'is_paid',
            'is_payable',
            'is_refund',
            'is_sealed',
            'name',
            'common_country_id',
            'common_domain_id',
            'iam_account_id',
            'iam_user_id',
            'iam_account_type_id',
            'accounting_identifier',
            'credit',
            'common_currency_id',
            'accounting_account_id',
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
    'term_year' => 'integer',
    'term_month' => 'integer',
    'is_paid' => 'boolean',
    'is_payable' => 'boolean',
    'is_refund' => 'boolean',
    'is_sealed' => 'boolean',
    'name' => 'string',
    'common_country_id' => 'integer',
    'common_domain_id' => 'integer',
    'iam_account_type_id' => 'integer',
    'accounting_identifier' => 'string',
    'common_currency_id' => 'integer',
    'accounting_account_id' => 'integer',
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
        parent::observe(InvoicesPerspectiveObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_invoices_perspective');

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

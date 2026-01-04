<?php

namespace NextDeveloper\Accounting\Database\Models;

use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\MonthlyPaidInvoicesPerformanceObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * MonthlyPaidInvoicesPerformance model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property \Carbon\Carbon $month_start
 * @property \Carbon\Carbon $month_end
 * @property string $month_name
 * @property string $month_code
 * @property integer $common_currency_id
 * @property integer $count
 * @property $total_amount
 * @property $avg_amount
 * @property $min_amount
 * @property $max_amount
 */
class MonthlyPaidInvoicesPerformance extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;

    public $timestamps = false;

    protected $table = 'accounting_monthly_paid_invoices_performance';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'month_start',
            'month_end',
            'month_name',
            'month_code',
            'common_currency_id',
            'count',
            'total_amount',
            'avg_amount',
            'min_amount',
            'max_amount',
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
    'month_start' => 'datetime',
    'month_end' => 'datetime',
    'month_name' => 'string',
    'month_code' => 'string',
    'common_currency_id' => 'integer',
    'count' => 'integer',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [
    'month_start',
    'month_end',
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
        parent::observe(MonthlyPaidInvoicesPerformanceObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_monthly_paid_invoices_performance');

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

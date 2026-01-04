<?php

namespace NextDeveloper\Accounting\Database\Models;

use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\DistributorSalesReportsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * DistributorSalesReports model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property integer $iam_account_id
 * @property integer $distributor_id
 * @property string $currency_code
 * @property integer $invoice_count
 * @property integer $unpaid_invoice_count
 * @property $paid_amount
 * @property $unpaid_amount
 * @property $this_month_income
 */
class DistributorSalesReports extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;

    public $timestamps = false;

    protected $table = 'accounting_distributor_sales_report';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'iam_account_id',
            'distributor_id',
            'currency_code',
            'invoice_count',
            'unpaid_invoice_count',
            'paid_amount',
            'unpaid_amount',
            'this_month_income',
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
    'distributor_id' => 'integer',
    'currency_code' => 'string',
    'invoice_count' => 'integer',
    'unpaid_invoice_count' => 'integer',
    ];

    /**
     We are casting data fields.
     *
     @var array
     */
    protected $dates = [

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
        parent::observe(DistributorSalesReportsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_distributor_sales_report');

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

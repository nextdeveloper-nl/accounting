<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Accounting\Database\Observers\AccountsPerspectiveObserver;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Commons\Database\Traits\HasStates;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\UuidId;
use Illuminate\Notifications\Notifiable;
use NextDeveloper\Commons\Database\Traits\HasObject;

/**
 * AccountsPerspective model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property string $name
 * @property string $phone_number
 * @property integer $common_country_id
 * @property integer $common_domain_id
 * @property integer $iam_user_id
 * @property integer $iam_account_type_id
 * @property integer $iam_account_id
 * @property string $tax_number
 * @property string $tax_office
 * @property string $accounting_identifier
 * @property $credit
 * @property integer $common_currency_id
 * @property string $common_currency_code
 * @property string $tr_mersis
 * @property string $trade_office
 * @property string $trade_office_number
 * @property string $distributor_partner
 * @property string $integrator_partner
 * @property string $sales_partner
 * @property string $affiliate_partner
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class AccountsPerspective extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_accounts_perspective';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'name',
            'phone_number',
            'common_country_id',
            'common_domain_id',
            'iam_user_id',
            'iam_account_type_id',
            'iam_account_id',
            'tax_number',
            'tax_office',
            'accounting_identifier',
            'credit',
            'common_currency_id',
            'common_currency_code',
            'tr_mersis',
            'trade_office',
            'trade_office_number',
            'distributor_partner',
            'integrator_partner',
            'sales_partner',
            'affiliate_partner',
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
    'name' => 'string',
    'phone_number' => 'string',
    'common_country_id' => 'integer',
    'common_domain_id' => 'integer',
    'iam_account_type_id' => 'integer',
    'tax_number' => 'string',
    'tax_office' => 'string',
    'accounting_identifier' => 'string',
    'common_currency_id' => 'integer',
    'common_currency_code' => 'string',
    'tr_mersis' => 'string',
    'trade_office' => 'string',
    'trade_office_number' => 'string',
    'distributor_partner' => 'string',
    'integrator_partner' => 'string',
    'sales_partner' => 'string',
    'affiliate_partner' => 'string',
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
        parent::observe(AccountsPerspectiveObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_accounts_perspective');

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

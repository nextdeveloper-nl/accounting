<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\ContractItemsPerspectiveObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * ContractItemsPerspective model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property string $object_type
 * @property integer $object_id
 * @property integer $accounting_contract_id
 * @property \Carbon\Carbon $term_starts
 * @property \Carbon\Carbon $term_ends
 * @property $price
 * @property integer $discount
 * @property integer $common_currency_id
 * @property string $contract_type
 * @property boolean $is_signed
 * @property boolean $is_approved
 * @property string $account_name
 * @property integer $iam_account_id
 * @property integer $iam_user_id
 * @property integer $iam_account_type_id
 * @property string $accounting_identifier
 * @property $credit
 * @property integer $accounting_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class ContractItemsPerspective extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_contract_items_perspective';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'object_type',
            'object_id',
            'accounting_contract_id',
            'term_starts',
            'term_ends',
            'price',
            'discount',
            'common_currency_id',
            'contract_type',
            'is_signed',
            'is_approved',
            'account_name',
            'iam_account_id',
            'iam_user_id',
            'iam_account_type_id',
            'accounting_identifier',
            'credit',
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
    'object_type' => 'string',
    'object_id' => 'integer',
    'accounting_contract_id' => 'integer',
    'term_starts' => 'datetime',
    'term_ends' => 'datetime',
    'discount' => 'integer',
    'common_currency_id' => 'integer',
    'contract_type' => 'string',
    'is_signed' => 'boolean',
    'is_approved' => 'boolean',
    'account_name' => 'string',
    'iam_account_type_id' => 'integer',
    'accounting_identifier' => 'string',
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
    'term_starts',
    'term_ends',
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
        parent::observe(ContractItemsPerspectiveObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_contract_items_perspective');

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

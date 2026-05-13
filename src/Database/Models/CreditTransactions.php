<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\CreditTransactionsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
* CreditTransactions model.
*
* @package NextDeveloper\Accounting\Database\Models
* @property integer $id
* @property string $uuid
* @property integer $accounting_account_id
* @property  $amount
* @property string $type
* @property  $balance_after
* @property string $object_type
* @property integer $object_id
* @property string $description
* @property integer $iam_account_id
* @property integer $iam_user_id
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property \Carbon\Carbon $deleted_at
*/
class CreditTransactions extends Model
{
use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
	use SoftDeletes;

	public $timestamps = true;

protected $table = 'accounting_credit_transactions';


/**
* @var array
*/
protected $guarded = [];

protected $fillable = [
            'accounting_account_id',
            'amount',
            'type',
            'balance_after',
            'object_type',
            'object_id',
            'description',
            'iam_account_id',
            'iam_user_id',
    ];

/**
*  Here we have the fulltext fields. We can use these for fulltext search if enabled.
*/
protected $fullTextFields = [

];

/**
* @var array
*/
protected $appends = [

];

/**
* We are casting fields to objects so that we can work on them better
* @var array
*/
protected $casts = [
'id' => 'integer',
'accounting_account_id' => 'integer',
'type' => 'string',
'object_type' => 'string',
'object_id' => 'integer',
'description' => 'string',
'created_at' => 'datetime',
'updated_at' => 'datetime',
'deleted_at' => 'datetime',
];

/**
* We are casting data fields.
* @var array
*/
protected $dates = [
'created_at',
'updated_at',
'deleted_at',
];

/**
* @var array
*/
protected $with = [

];

/**
* @var int
*/
protected $perPage = 20;

/**
* @return void
*/
public static function boot()
{
parent::boot();

//  We create and add Observer even if we wont use it.
parent::observe(CreditTransactionsObserver::class);

self::registerScopes();
}

public static function registerScopes()
{
$globalScopes = config('accounting.scopes.global');
$modelScopes = config('accounting.scopes.accounting_credit_transactions');

if(!$modelScopes) $modelScopes = [];
if (!$globalScopes) $globalScopes = [];

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
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

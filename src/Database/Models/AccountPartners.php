<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\AccountPartnersObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
* AccountPartners model.
*
* @package NextDeveloper\Accounting\Database\Models
* @property integer $id
* @property string $uuid
* @property integer $tenant_account_id
* @property integer $accounting_account_id
* @property integer $partnership_role_id
* @property integer $partnership_id
* @property \Carbon\Carbon $started_at
* @property \Carbon\Carbon $finished_at
* @property integer $iam_account_id
* @property integer $iam_user_id
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property \Carbon\Carbon $deleted_at
*/
class AccountPartners extends Model
{
use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
	use SoftDeletes;

	public $timestamps = true;

protected $table = 'accounting_account_partners';


/**
* @var array
*/
protected $guarded = [];

protected $fillable = [
            'tenant_account_id',
            'accounting_account_id',
            'partnership_role_id',
            'partnership_id',
            'started_at',
            'finished_at',
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
'tenant_account_id' => 'integer',
'accounting_account_id' => 'integer',
'partnership_role_id' => 'integer',
'partnership_id' => 'integer',
'started_at' => 'datetime',
'finished_at' => 'datetime',
'iam_account_id' => 'integer',
'iam_user_id' => 'integer',
'created_at' => 'datetime',
'updated_at' => 'datetime',
'deleted_at' => 'datetime',
];

/**
* We are casting data fields.
* @var array
*/
protected $dates = [
'started_at',
'finished_at',
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
parent::observe(AccountPartnersObserver::class);

self::registerScopes();
}

public static function registerScopes()
{
$globalScopes = config('accounting.scopes.global');
$modelScopes = config('accounting.scopes.accounting_account_partners');

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

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}

<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\InvoiceCommissionsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
* InvoiceCommissions model.
*
* @package NextDeveloper\Accounting\Database\Models
* @property integer $id
* @property string $uuid
* @property integer $tenant_account_id
* @property integer $accounting_invoice_id
* @property integer $partnership_role_id
* @property integer $partnership_id
* @property integer $level
* @property integer $commission_invoice_id
* @property integer $iam_account_id
* @property integer $iam_user_id
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property \Carbon\Carbon $deleted_at
*/
class InvoiceCommissions extends Model
{
use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
	use SoftDeletes;

	public $timestamps = true;

protected $table = 'accounting_invoice_commissions';


/**
* @var array
*/
protected $guarded = [];

protected $fillable = [
            'tenant_account_id',
            'accounting_invoice_id',
            'partnership_role_id',
            'partnership_id',
            'level',
            'commission_invoice_id',
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
'accounting_invoice_id' => 'integer',
'partnership_role_id' => 'integer',
'partnership_id' => 'integer',
'level' => 'integer',
'commission_invoice_id' => 'integer',
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
parent::observe(InvoiceCommissionsObserver::class);

self::registerScopes();
}

public static function registerScopes()
{
$globalScopes = config('accounting.scopes.global');
$modelScopes = config('accounting.scopes.accounting_invoice_commissions');

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

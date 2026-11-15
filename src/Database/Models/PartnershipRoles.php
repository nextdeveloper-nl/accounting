<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\PartnershipRolesObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
* PartnershipRoles model.
*
* @package NextDeveloper\Accounting\Database\Models
* @property integer $id
* @property string $uuid
* @property integer $tenant_account_id
* @property string $slug
* @property string $name
* @property string $description
* @property float $commission_rate
* @property float $parent_override_rate
* @property string $commission_basis
* @property integer $priority
* @property boolean $allows_multiple_per_customer
* @property boolean $is_default_for_signup_code
* @property array $allowed_child_role_ids
* @property array $permissions
* @property string $status
* @property integer $iam_account_id
* @property integer $iam_user_id
* @property \Carbon\Carbon $created_at
* @property \Carbon\Carbon $updated_at
* @property \Carbon\Carbon $deleted_at
*/
class PartnershipRoles extends Model
{
use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
	use SoftDeletes;

	public $timestamps = true;

protected $table = 'accounting_partnership_roles';


/**
* @var array
*/
protected $guarded = [];

protected $fillable = [
            'tenant_account_id',
            'slug',
            'name',
            'description',
            'commission_rate',
            'parent_override_rate',
            'commission_basis',
            'priority',
            'allows_multiple_per_customer',
            'is_default_for_signup_code',
            'allowed_child_role_ids',
            'permissions',
            'status',
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
'slug' => 'string',
'name' => 'string',
'description' => 'string',
'commission_rate' => 'double',
'parent_override_rate' => 'double',
'commission_basis' => 'string',
'priority' => 'integer',
'allows_multiple_per_customer' => 'boolean',
'is_default_for_signup_code' => 'boolean',
'allowed_child_role_ids' => 'array',
'permissions' => 'array',
'status' => 'string',
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
parent::observe(PartnershipRolesObserver::class);

self::registerScopes();
}

public static function registerScopes()
{
$globalScopes = config('accounting.scopes.global');
$modelScopes = config('accounting.scopes.accounting_partnership_roles');

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

<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\PartnershipsObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;
use NextDeveloper\Commons\Database\Traits\HasObject;

/**
 * Partnerships model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property integer $iam_account_id
 * @property boolean $is_brand_ambassador
 * @property integer $customer_count
 * @property integer $level
 * @property integer $reward_points
 * @property $boosts
 * @property $mystery_box
 * @property $badges
 * @property boolean $is_approved
 * @property array $technical_capabilities
 * @property string $industry
 * @property array $sector_focus
 * @property array $special_interest
 * @property array $compliance_certifications
 * @property array $target_group
 * @property string $meeting_link
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property array $operating_countries
 * @property array $operating_cities
 */
class Partnerships extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_partnerships';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'iam_account_id',
            'is_brand_ambassador',
            'customer_count',
            'level',
            'reward_points',
            'boosts',
            'mystery_box',
            'badges',
            'is_approved',
            'technical_capabilities',
            'industry',
            'sector_focus',
            'special_interest',
            'compliance_certifications',
            'target_group',
            'meeting_link',
            'operating_countries',
            'operating_cities',
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
    'is_brand_ambassador' => 'boolean',
    'customer_count' => 'integer',
    'level' => 'integer',
    'reward_points' => 'integer',
    'boosts' => 'array',
    'mystery_box' => 'array',
    'badges' => 'array',
    'is_approved' => 'boolean',
    'technical_capabilities' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'industry' => 'string',
    'sector_focus' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'special_interest' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'compliance_certifications' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'target_group' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'meeting_link' => 'string',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
    'deleted_at' => 'datetime',
    'operating_countries' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
    'operating_cities' => \NextDeveloper\Commons\Database\Casts\TextArray::class,
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
        parent::observe(PartnershipsObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_partnerships');

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
        return $this->belongsTo(\NextDeveloper\IAM\Database\Models\Accounts::class);
    }
    
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE













}

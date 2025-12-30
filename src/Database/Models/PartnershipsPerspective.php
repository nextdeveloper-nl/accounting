<?php

namespace NextDeveloper\Accounting\Database\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use NextDeveloper\Commons\Database\Traits\HasStates;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;
use NextDeveloper\Commons\Database\Traits\Filterable;
use NextDeveloper\Accounting\Database\Observers\PartnershipsPerspectiveObserver;
use NextDeveloper\Commons\Database\Traits\UuidId;
use NextDeveloper\Commons\Database\Traits\HasObject;
use NextDeveloper\Commons\Common\Cache\Traits\CleanCache;
use NextDeveloper\Commons\Database\Traits\Taggable;
use NextDeveloper\Commons\Database\Traits\RunAsAdministrator;

/**
 * PartnershipsPerspective model.
 *
 * @package  NextDeveloper\Accounting\Database\Models
 * @property integer $id
 * @property string $uuid
 * @property string $name
 * @property string $partner_code
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
 * @property integer $iam_account_id
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 */
class PartnershipsPerspective extends Model
{
    use Filterable, UuidId, CleanCache, Taggable, HasStates, RunAsAdministrator, HasObject;
    use SoftDeletes;

    public $timestamps = true;

    protected $table = 'accounting_partnerships_perspective';


    /**
     @var array
     */
    protected $guarded = [];

    protected $fillable = [
            'name',
            'partner_code',
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
            'iam_account_id',
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
    'partner_code' => 'string',
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
        parent::observe(PartnershipsPerspectiveObserver::class);

        self::registerScopes();
    }

    public static function registerScopes()
    {
        $globalScopes = config('accounting.scopes.global');
        $modelScopes = config('accounting.scopes.accounting_partnerships_perspective');

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

<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Commons\Database\Models\Addresses;
use NextDeveloper\Commons\Database\Models\Comments;
use NextDeveloper\Commons\Database\Models\Meta;
use NextDeveloper\Commons\Database\Models\PhoneNumbers;
use NextDeveloper\Commons\Database\Models\SocialMedia;
use NextDeveloper\Commons\Database\Models\Votes;
use NextDeveloper\Commons\Database\Models\Media;
use NextDeveloper\Commons\Http\Transformers\MediaTransformer;
use NextDeveloper\Commons\Database\Models\AvailableActions;
use NextDeveloper\Commons\Http\Transformers\AvailableActionsTransformer;
use NextDeveloper\Commons\Database\Models\States;
use NextDeveloper\Commons\Http\Transformers\StatesTransformer;
use NextDeveloper\Commons\Http\Transformers\CommentsTransformer;
use NextDeveloper\Commons\Http\Transformers\SocialMediaTransformer;
use NextDeveloper\Commons\Http\Transformers\MetaTransformer;
use NextDeveloper\Commons\Http\Transformers\VotesTransformer;
use NextDeveloper\Commons\Http\Transformers\AddressesTransformer;
use NextDeveloper\Commons\Http\Transformers\PhoneNumbersTransformer;
use NextDeveloper\Accounting\Database\Models\PartnershipZones;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * Class PartnershipZonesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractPartnershipZonesTransformer extends AbstractTransformer {

    /**
    * @var array
    */
    protected array $availableIncludes = [
        'states',
        'actions',
        'media',
        'comments',
        'votes',
        'socialMedia',
        'phoneNumbers',
        'addresses',
        'meta'
    ];

    /**
     * @param PartnershipZones $model
     *
     * @return array
     */
    public function transform(PartnershipZones $model) {
        $tenantAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->tenant_account_id)->first();
        $partnershipId = \NextDeveloper\Accounting\Database\Models\Partnerships::where('id', $model->partnership_id)->first();
        $partnershipRoleId = \NextDeveloper\Accounting\Database\Models\PartnershipRoles::where('id', $model->partnership_role_id)->first();
        $commonCountryId = \NextDeveloper\Commons\Database\Models\Countries::where('id', $model->common_country_id)->first();
        $commonCityId = \NextDeveloper\Commons\Database\Models\Cities::where('id', $model->common_city_id)->first();
        $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
        $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();

        return $this->buildPayload([
'id'  =>  $model->uuid,
'tenant_account_id'  =>  $tenantAccountId ? $tenantAccountId->uuid : null,
'partnership_id'  =>  $partnershipId ? $partnershipId->uuid : null,
'partnership_role_id'  =>  $partnershipRoleId ? $partnershipRoleId->uuid : null,
'common_country_id'  =>  $commonCountryId ? $commonCountryId->uuid : null,
'common_city_id'  =>  $commonCityId ? $commonCityId->uuid : null,
'region'  =>  $model->region,
'is_exclusive'  =>  $model->is_exclusive,
'priority'  =>  $model->priority,
'status'  =>  $model->status,
'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
'created_at'  =>  $model->created_at,
'updated_at'  =>  $model->updated_at,
'deleted_at'  =>  $model->deleted_at,
    ]);
    }

    public function includeStates(PartnershipZones $model)
    {
        $States = States::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($States, new StatesTransformer());
    }

    public function includeMedia(PartnershipZones $model)
    {
        $Media = Media::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($Media, new MediaTransformer());
    }

    public function includeSocialMedia(PartnershipZones $model)
    {
        $SocialMedia = SocialMedia::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($SocialMedia, new SocialMediaTransformer());
    }

    public function includeComments(PartnershipZones $model)
    {
        $Comments = Comments::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($Comments, new CommentsTransformer());
    }

    public function includeVotes(PartnershipZones $model)
    {
        $Votes = Votes::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($Votes, new VotesTransformer());
    }

    public function includeMeta(PartnershipZones $model)
    {
        $meta = Meta::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($meta, new MetaTransformer());
    }

    public function includePhoneNumbers(PartnershipZones $model)
    {
        $PhoneNumbers = PhoneNumbers::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($PhoneNumbers, new PhoneNumbersTransformer());
    }

    public function includeAddresses(PartnershipZones $model)
    {
        $Addresses = Addresses::where('object_type', get_class($model))
            ->where('object_id', $model->id)
            ->get();

        return $this->collection($Addresses, new AddressesTransformer());
    }

    public function includeActions(PartnershipZones $model)
    {
        $input = get_class($model);
        $input = str_replace('\\Database\\Models', '', $input);

        $actions = AvailableActions::withoutGlobalScope(AuthorizationScope::class)
            ->where('input', $input)
            ->get();

        return $this->collection($actions, new AvailableActionsTransformer());
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}

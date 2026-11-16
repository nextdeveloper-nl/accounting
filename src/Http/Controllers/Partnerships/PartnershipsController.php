<?php

namespace NextDeveloper\Accounting\Http\Controllers\Partnerships;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\Partnerships\PartnershipsUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\PartnershipsQueryFilter;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Services\PartnershipsService;
use NextDeveloper\Accounting\Http\Requests\Partnerships\PartnershipsCreateRequest;
use NextDeveloper\Accounting\Http\Requests\Partnerships\PartnershipInviteRequest;
use NextDeveloper\Accounting\Http\Requests\Partnerships\PartnershipVisibilityRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class PartnershipsController extends AbstractController
{
    private $model = Partnerships::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of partnerships.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PartnershipsQueryFilter $filter  An object that builds search query
     * @param  Request                 $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PartnershipsQueryFilter $filter, Request $request)
    {
        $data = PartnershipsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = PartnershipsService::getActions();

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * Makes the related action to the object
     *
     * @param  $objectId
     * @param  $action
     * @return array
     */
    public function doAction($objectId, $action)
    {
        $actionId = PartnershipsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $partnershipsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PartnershipsService::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method returns the list of sub objects the related object. Sub object means an object which is preowned by
     * this object.
     *
     * It can be tags, addresses, states etc.
     *
     * @param  $ref
     * @param  $subObject
     * @return void
     */
    public function relatedObjects($ref, $subObject)
    {
        $objects = PartnershipsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created Partnerships object on database.
     *
     * @param  PartnershipsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PartnershipsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnershipsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Partnerships object on database.
     *
     * @param  $partnershipsId
     * @param  PartnershipsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($partnershipsId, PartnershipsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnershipsService::update($partnershipsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Partnerships object on database.
     *
     * @param  $partnershipsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($partnershipsId)
    {
        $model = PartnershipsService::delete($partnershipsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * The account the user acts as (the tenant) invites an existing company to be its partner.
     * The operation itself, including every authorization check, lives in PartnershipsService.
     *
     * @param  PartnershipInviteRequest $request
     * @return mixed|null
     */
    public function invite(PartnershipInviteRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        //  Invite by email: the answer is the same text in every case, see PartnershipsService::invitePartnerByEmail().
        if (!$request->validated('partner_account_id')) {
            return $this->withArray(PartnershipsService::invitePartnerByEmail(
                $request->validated('partner_email'),
                $request->validated('role_ids'),
                $request->validated('parent_partnership_id')
            ));
        }

        $model = PartnershipsService::invitePartner(
            $request->validated('partner_account_id'),
            $request->validated('role_ids'),
            $request->validated('parent_partnership_id')
        );

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * Candidate companies for the invite drawer: ?q=<at least 3 characters of the company name>.
     *
     * @param  Request $request
     * @return mixed|null
     */
    public function inviteCandidates(Request $request)
    {
        return $this->withArray([
            'data' => PartnershipsService::findInviteCandidates((string)$request->query('q', ''))
        ]);
    }

    /**
     * The tenant decides who sees this partner in the partner lists (private, customers, public).
     *
     * @param  $partnershipId
     * @param  PartnershipVisibilityRequest $request
     * @return mixed|null
     */
    public function visibility($partnershipId, PartnershipVisibilityRequest $request)
    {
        $model = PartnershipsService::setVisibility($partnershipId, $request->validated('visibility'));

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * The invited company accepts the invitation.
     *
     * @param  $partnershipId
     * @return mixed|null
     */
    public function accept($partnershipId)
    {
        $model = PartnershipsService::acceptInvitation($partnershipId);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * The invited company rejects the invitation.
     *
     * @param  $partnershipId
     * @return mixed|null
     */
    public function reject($partnershipId)
    {
        $model = PartnershipsService::rejectInvitation($partnershipId);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * The tenant ends a partnership.
     *
     * @param  $partnershipId
     * @return mixed|null
     */
    public function revoke($partnershipId)
    {
        $model = PartnershipsService::revokePartner($partnershipId);

        return ResponsableFactory::makeResponse($this, $model);
    }

}

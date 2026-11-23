<?php

namespace NextDeveloper\Accounting\Http\Controllers\PartnerPrograms;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\PartnerPrograms\PartnerProgramsUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\PartnerProgramsQueryFilter;
use NextDeveloper\Accounting\Database\Models\PartnerPrograms;
use NextDeveloper\Accounting\Services\PartnerProgramsService;
use NextDeveloper\Accounting\Http\Requests\PartnerPrograms\PartnerProgramsCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class PartnerProgramsController extends AbstractController
{
    private $model = PartnerPrograms::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of partnerprograms.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PartnerProgramsQueryFilter $filter  An object that builds search query
     * @param  Request                       $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PartnerProgramsQueryFilter $filter, Request $request)
    {
        $data = PartnerProgramsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = PartnerProgramsService::getActions();

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
        $actionId = PartnerProgramsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $partnerProgramsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PartnerProgramsService::getByRef($ref);

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
        $objects = PartnerProgramsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created PartnerPrograms object on database.
     *
     * @param  PartnerProgramsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PartnerProgramsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnerProgramsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PartnerPrograms object on database.
     *
     * @param  $partnerProgramsId
     * @param  PartnerProgramsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($partnerProgramsId, PartnerProgramsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnerProgramsService::update($partnerProgramsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method deletes PartnerPrograms object on database.
     *
     * @param  $partnerProgramsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($partnerProgramsId)
    {
        $model = PartnerProgramsService::delete($partnerProgramsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Summary of the partner program state of the account the user acts as (drives navigation visibility).
     *
     * @return mixed|null
     */
    public function me()
    {
        return $this->withArray(PartnerProgramsService::getSummary());
    }

    /**
     * Programs of other companies that take applications (?q=<name prefix>&page=&per_page=). The rules live in
     * PartnerProgramsService.
     *
     * @param  Request $request
     * @return mixed|null
     */
    public function discover(Request $request)
    {
        return $this->withArray(PartnerProgramsService::discoverPrograms(
            $request->query('q'),
            (int)$request->query('page', 1),
            (int)$request->query('per_page', 20)
        ));
    }

    /**
     * The owner of the account turns it into a partner program (self service onboarding).
     *
     * @return mixed|null
     */
    public function enable()
    {
        $model = PartnerProgramsService::enableProgram();

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * The owner turns the partner program off (no new invitations, existing partners are untouched).
     *
     * @return mixed|null
     */
    public function disable()
    {
        $model = PartnerProgramsService::disableProgram();

        return ResponsableFactory::makeResponse($this, $model);
    }

}

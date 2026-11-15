<?php

namespace NextDeveloper\Accounting\Http\Controllers\PartnershipRoleAssignments;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\PartnershipRoleAssignments\PartnershipRoleAssignmentsUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\PartnershipRoleAssignmentsQueryFilter;
use NextDeveloper\Accounting\Database\Models\PartnershipRoleAssignments;
use NextDeveloper\Accounting\Services\PartnershipRoleAssignmentsService;
use NextDeveloper\Accounting\Http\Requests\PartnershipRoleAssignments\PartnershipRoleAssignmentsCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class PartnershipRoleAssignmentsController extends AbstractController
{
    private $model = PartnershipRoleAssignments::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of partnershiproleassignments.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PartnershipRoleAssignmentsQueryFilter $filter  An object that builds search query
     * @param  Request                       $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PartnershipRoleAssignmentsQueryFilter $filter, Request $request)
    {
        $data = PartnershipRoleAssignmentsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = PartnershipRoleAssignmentsService::getActions();

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
        $actionId = PartnershipRoleAssignmentsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $partnershipRoleAssignmentsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PartnershipRoleAssignmentsService::getByRef($ref);

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
        $objects = PartnershipRoleAssignmentsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created PartnershipRoleAssignments object on database.
     *
     * @param  PartnershipRoleAssignmentsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PartnershipRoleAssignmentsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnershipRoleAssignmentsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PartnershipRoleAssignments object on database.
     *
     * @param  $partnershipRoleAssignmentsId
     * @param  PartnershipRoleAssignmentsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($partnershipRoleAssignmentsId, PartnershipRoleAssignmentsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnershipRoleAssignmentsService::update($partnershipRoleAssignmentsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method deletes PartnershipRoleAssignments object on database.
     *
     * @param  $partnershipRoleAssignmentsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($partnershipRoleAssignmentsId)
    {
        $model = PartnershipRoleAssignmentsService::delete($partnershipRoleAssignmentsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

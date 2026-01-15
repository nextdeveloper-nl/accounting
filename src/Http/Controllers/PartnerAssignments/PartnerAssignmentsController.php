<?php

namespace NextDeveloper\Accounting\Http\Controllers\PartnerAssignments;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\PartnerAssignments\PartnerAssignmentsUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\PartnerAssignmentsQueryFilter;
use NextDeveloper\Accounting\Database\Models\PartnerAssignments;
use NextDeveloper\Accounting\Services\PartnerAssignmentsService;
use NextDeveloper\Accounting\Http\Requests\PartnerAssignments\PartnerAssignmentsCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class PartnerAssignmentsController extends AbstractController
{
    private $model = PartnerAssignments::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of partnerassignments.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PartnerAssignmentsQueryFilter $filter  An object that builds search query
     * @param  Request                       $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PartnerAssignmentsQueryFilter $filter, Request $request)
    {
        $data = PartnerAssignmentsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = PartnerAssignmentsService::getActions();

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
        $actionId = PartnerAssignmentsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $partnerAssignmentsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PartnerAssignmentsService::getByRef($ref);

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
        $objects = PartnerAssignmentsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created PartnerAssignments object on database.
     *
     * @param  PartnerAssignmentsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PartnerAssignmentsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnerAssignmentsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PartnerAssignments object on database.
     *
     * @param  $partnerAssignmentsId
     * @param  PartnerAssignmentsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($partnerAssignmentsId, PartnerAssignmentsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PartnerAssignmentsService::update($partnerAssignmentsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PartnerAssignments object on database.
     *
     * @param  $partnerAssignmentsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($partnerAssignmentsId)
    {
        $model = PartnerAssignmentsService::delete($partnerAssignmentsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

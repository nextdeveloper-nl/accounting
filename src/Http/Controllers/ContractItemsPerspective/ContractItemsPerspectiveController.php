<?php

namespace NextDeveloper\Accounting\Http\Controllers\ContractItemsPerspective;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\ContractItemsPerspectiveQueryFilter;
use NextDeveloper\Accounting\Database\Models\ContractItemsPerspective;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Accounting\Http\Requests\ContractItemsPerspective\ContractItemsPerspectiveCreateRequest;
use NextDeveloper\Accounting\Http\Requests\ContractItemsPerspective\ContractItemsPerspectiveUpdateRequest;
use NextDeveloper\Accounting\Services\ContractItemsPerspectiveService;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;

class ContractItemsPerspectiveController extends AbstractController
{
    private $model = ContractItemsPerspective::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of contractitemsperspectives.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  ContractItemsPerspectiveQueryFilter $filter  An object that builds search query
     * @param  Request                             $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ContractItemsPerspectiveQueryFilter $filter, Request $request)
    {
        $data = ContractItemsPerspectiveService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = ContractItemsPerspectiveService::getActions();

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
        $actionId = ContractItemsPerspectiveService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $contractItemsPerspectiveId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = ContractItemsPerspectiveService::getByRef($ref);

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
        $objects = ContractItemsPerspectiveService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created ContractItemsPerspective object on database.
     *
     * @param  ContractItemsPerspectiveCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(ContractItemsPerspectiveCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = ContractItemsPerspectiveService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates ContractItemsPerspective object on database.
     *
     * @param  $contractItemsPerspectiveId
     * @param  ContractItemsPerspectiveUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($contractItemsPerspectiveId, ContractItemsPerspectiveUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = ContractItemsPerspectiveService::update($contractItemsPerspectiveId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates ContractItemsPerspective object on database.
     *
     * @param  $contractItemsPerspectiveId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($contractItemsPerspectiveId)
    {
        $model = ContractItemsPerspectiveService::delete($contractItemsPerspectiveId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

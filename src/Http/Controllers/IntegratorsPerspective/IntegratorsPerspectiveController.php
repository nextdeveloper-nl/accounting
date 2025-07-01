<?php

namespace NextDeveloper\Accounting\Http\Controllers\IntegratorsPerspective;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\IntegratorsPerspective\IntegratorsPerspectiveUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\IntegratorsPerspectiveQueryFilter;
use NextDeveloper\Accounting\Database\Models\IntegratorsPerspective;
use NextDeveloper\Accounting\Services\IntegratorsPerspectiveService;
use NextDeveloper\Accounting\Http\Requests\IntegratorsPerspective\IntegratorsPerspectiveCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class IntegratorsPerspectiveController extends AbstractController
{
    private $model = IntegratorsPerspective::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of integratorsperspectives.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  IntegratorsPerspectiveQueryFilter $filter  An object that builds search query
     * @param  Request                           $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(IntegratorsPerspectiveQueryFilter $filter, Request $request)
    {
        $data = IntegratorsPerspectiveService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = IntegratorsPerspectiveService::getActions();

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
        $actionId = IntegratorsPerspectiveService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $integratorsPerspectiveId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = IntegratorsPerspectiveService::getByRef($ref);

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
        $objects = IntegratorsPerspectiveService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created IntegratorsPerspective object on database.
     *
     * @param  IntegratorsPerspectiveCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(IntegratorsPerspectiveCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = IntegratorsPerspectiveService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates IntegratorsPerspective object on database.
     *
     * @param  $integratorsPerspectiveId
     * @param  IntegratorsPerspectiveUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($integratorsPerspectiveId, IntegratorsPerspectiveUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = IntegratorsPerspectiveService::update($integratorsPerspectiveId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates IntegratorsPerspective object on database.
     *
     * @param  $integratorsPerspectiveId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($integratorsPerspectiveId)
    {
        $model = IntegratorsPerspectiveService::delete($integratorsPerspectiveId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

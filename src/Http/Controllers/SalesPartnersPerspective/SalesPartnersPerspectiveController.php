<?php

namespace NextDeveloper\Accounting\Http\Controllers\SalesPartnersPerspective;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\SalesPartnersPerspective\SalesPartnersPerspectiveUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\SalesPartnersPerspectiveQueryFilter;
use NextDeveloper\Accounting\Database\Models\SalesPartnersPerspective;
use NextDeveloper\Accounting\Services\SalesPartnersPerspectiveService;
use NextDeveloper\Accounting\Http\Requests\SalesPartnersPerspective\SalesPartnersPerspectiveCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class SalesPartnersPerspectiveController extends AbstractController
{
    private $model = SalesPartnersPerspective::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of salespartnersperspectives.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  SalesPartnersPerspectiveQueryFilter $filter  An object that builds search query
     * @param  Request                             $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(SalesPartnersPerspectiveQueryFilter $filter, Request $request)
    {
        $data = SalesPartnersPerspectiveService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = SalesPartnersPerspectiveService::getActions();

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
        $actionId = SalesPartnersPerspectiveService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $salesPartnersPerspectiveId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = SalesPartnersPerspectiveService::getByRef($ref);

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
        $objects = SalesPartnersPerspectiveService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created SalesPartnersPerspective object on database.
     *
     * @param  SalesPartnersPerspectiveCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(SalesPartnersPerspectiveCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = SalesPartnersPerspectiveService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates SalesPartnersPerspective object on database.
     *
     * @param  $salesPartnersPerspectiveId
     * @param  SalesPartnersPerspectiveUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($salesPartnersPerspectiveId, SalesPartnersPerspectiveUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = SalesPartnersPerspectiveService::update($salesPartnersPerspectiveId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates SalesPartnersPerspective object on database.
     *
     * @param  $salesPartnersPerspectiveId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($salesPartnersPerspectiveId)
    {
        $model = SalesPartnersPerspectiveService::delete($salesPartnersPerspectiveId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

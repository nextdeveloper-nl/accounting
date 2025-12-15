<?php

namespace NextDeveloper\Accounting\Http\Controllers\DistributorSalesReports;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\DistributorSalesReports\DistributorSalesReportsUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\DistributorSalesReportsQueryFilter;
use NextDeveloper\Accounting\Database\Models\DistributorSalesReports;
use NextDeveloper\Accounting\Services\DistributorSalesReportsService;
use NextDeveloper\Accounting\Http\Requests\DistributorSalesReports\DistributorSalesReportsCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class DistributorSalesReportsController extends AbstractController
{
    private $model = DistributorSalesReports::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of distributorsalesreports.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  DistributorSalesReportsQueryFilter $filter  An object that builds search query
     * @param  Request                            $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(DistributorSalesReportsQueryFilter $filter, Request $request)
    {
        $data = DistributorSalesReportsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = DistributorSalesReportsService::getActions();

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
        $actionId = DistributorSalesReportsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $distributorSalesReportsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = DistributorSalesReportsService::getByRef($ref);

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
        $objects = DistributorSalesReportsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created DistributorSalesReports object on database.
     *
     * @param  DistributorSalesReportsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(DistributorSalesReportsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = DistributorSalesReportsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates DistributorSalesReports object on database.
     *
     * @param  $distributorSalesReportsId
     * @param  DistributorSalesReportsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($distributorSalesReportsId, DistributorSalesReportsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = DistributorSalesReportsService::update($distributorSalesReportsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates DistributorSalesReports object on database.
     *
     * @param  $distributorSalesReportsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($distributorSalesReportsId)
    {
        $model = DistributorSalesReportsService::delete($distributorSalesReportsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

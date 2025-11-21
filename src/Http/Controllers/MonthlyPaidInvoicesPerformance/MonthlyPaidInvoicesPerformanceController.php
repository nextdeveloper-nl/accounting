<?php

namespace NextDeveloper\Accounting\Http\Controllers\MonthlyPaidInvoicesPerformance;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\MonthlyPaidInvoicesPerformanceQueryFilter;
use NextDeveloper\Accounting\Database\Models\MonthlyPaidInvoicesPerformance;
use NextDeveloper\Accounting\Services\MonthlyPaidInvoicesPerformanceService;
use NextDeveloper\Accounting\Http\Requests\MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class MonthlyPaidInvoicesPerformanceController extends AbstractController
{
    private $model = MonthlyPaidInvoicesPerformance::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of monthlypaidinvoicesperformances.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  MonthlyPaidInvoicesPerformanceQueryFilter $filter  An object that builds search query
     * @param  Request                                   $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(MonthlyPaidInvoicesPerformanceQueryFilter $filter, Request $request)
    {
        $data = MonthlyPaidInvoicesPerformanceService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = MonthlyPaidInvoicesPerformanceService::getActions();

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
        $actionId = MonthlyPaidInvoicesPerformanceService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $monthlyPaidInvoicesPerformanceId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = MonthlyPaidInvoicesPerformanceService::getByRef($ref);

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
        $objects = MonthlyPaidInvoicesPerformanceService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created MonthlyPaidInvoicesPerformance object on database.
     *
     * @param  MonthlyPaidInvoicesPerformanceCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(MonthlyPaidInvoicesPerformanceCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = MonthlyPaidInvoicesPerformanceService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates MonthlyPaidInvoicesPerformance object on database.
     *
     * @param  $monthlyPaidInvoicesPerformanceId
     * @param  MonthlyPaidInvoicesPerformanceUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($monthlyPaidInvoicesPerformanceId, MonthlyPaidInvoicesPerformanceUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = MonthlyPaidInvoicesPerformanceService::update($monthlyPaidInvoicesPerformanceId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates MonthlyPaidInvoicesPerformance object on database.
     *
     * @param  $monthlyPaidInvoicesPerformanceId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($monthlyPaidInvoicesPerformanceId)
    {
        $model = MonthlyPaidInvoicesPerformanceService::delete($monthlyPaidInvoicesPerformanceId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

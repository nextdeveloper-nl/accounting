<?php

namespace NextDeveloper\Accounting\Http\Controllers\CreditTransactions;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\CreditTransactions\CreditTransactionsUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\CreditTransactionsQueryFilter;
use NextDeveloper\Accounting\Database\Models\CreditTransactions;
use NextDeveloper\Accounting\Services\CreditTransactionsService;
use NextDeveloper\Accounting\Http\Requests\CreditTransactions\CreditTransactionsCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class CreditTransactionsController extends AbstractController
{
    private $model = CreditTransactions::class;

use TagsTrait;
use AddressesTrait;
    /**
    * This method returns the list of credittransactions.
    *
    * optional http params:
    * - paginate: If you set paginate parameter, the result will be returned paginated.
    *
    * @param CreditTransactionsQueryFilter $filter An object that builds search query
    * @param Request $request Laravel request object, this holds all data about request. Automatically populated.
    * @return \Illuminate\Http\JsonResponse
    */
    public function index(CreditTransactionsQueryFilter $filter, Request $request) {
        $data = CreditTransactionsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
    * This function returns the list of actions that can be performed on this object.
    *
    * @return void
    */
    public function getActions()
    {
        $data = CreditTransactionsService::getActions();

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
    * Makes the related action to the object
    *
    * @param $objectId
    * @param $action
    * @return array
    */
    public function doAction($objectId, $action) {
        $actionId = CreditTransactionsService::doAction($objectId, $action, request()->all());

        return $this->withArray([
            'action_id' =>  $actionId
        ]);
    }

    /**
    * This method receives ID for the related model and returns the item to the client.
    *
    * @param $creditTransactionsId
    * @return mixed|null
    * @throws \Laravel\Octane\Exceptions\DdException
    */
    public function show($ref) {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = CreditTransactionsService::getByRef($ref);

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method returns the list of sub objects the related object. Sub object means an object which is preowned by
    * this object.
    *
    * It can be tags, addresses, states etc.
    *
    * @param $ref
    * @param $subObject
    * @return void
    */
    public function relatedObjects($ref, $subObject) {
        $objects = CreditTransactionsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
    * This method created CreditTransactions object on database.
    *
    * @param CreditTransactionsCreateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function store(CreditTransactionsCreateRequest $request) {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = CreditTransactionsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method updates CreditTransactions object on database.
    *
    * @param $creditTransactionsId
    * @param CreditTransactionsUpdateRequest $request
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function update($creditTransactionsId, CreditTransactionsUpdateRequest $request) {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = CreditTransactionsService::update($creditTransactionsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
    * This method updates CreditTransactions object on database.
    *
    * @param $creditTransactionsId
    * @return mixed|null
    * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
    */
    public function destroy($creditTransactionsId) {
        $model = CreditTransactionsService::delete($creditTransactionsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

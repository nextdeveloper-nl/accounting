<?php

namespace NextDeveloper\Accounting\Http\Controllers\InvoiceItems;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\InvoiceItemsQueryFilter;
use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Accounting\Http\Requests\InvoiceItems\InvoiceItemsCreateRequest;
use NextDeveloper\Accounting\Http\Requests\InvoiceItems\InvoiceItemsUpdateRequest;
use NextDeveloper\Accounting\Services\InvoiceItemsService;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Commons\Http\Traits\Addresses;
use NextDeveloper\Commons\Http\Traits\Tags;

class InvoiceItemsController extends AbstractController
{
    private $model = InvoiceItems::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of invoiceitems.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  InvoiceItemsQueryFilter $filter  An object that builds search query
     * @param  Request                 $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(InvoiceItemsQueryFilter $filter, Request $request)
    {
        $data = InvoiceItemsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $actions = InvoiceItemsService::getActions();

        if($actions) {
            if(array_key_exists($this->model, $actions)) {
                return $this->withArray($actions[$this->model]);
            }
        }

        return $this->noContent();
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
        $actionId = InvoiceItemsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $invoiceItemsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = InvoiceItemsService::getByRef($ref);

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
        $objects = InvoiceItemsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created InvoiceItems object on database.
     *
     * @param  InvoiceItemsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(InvoiceItemsCreateRequest $request)
    {
        $model = InvoiceItemsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates InvoiceItems object on database.
     *
     * @param  $invoiceItemsId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($invoiceItemsId, InvoiceItemsUpdateRequest $request)
    {
        $model = InvoiceItemsService::update($invoiceItemsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates InvoiceItems object on database.
     *
     * @param  $invoiceItemsId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($invoiceItemsId)
    {
        $model = InvoiceItemsService::delete($invoiceItemsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

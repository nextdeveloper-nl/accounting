<?php

namespace NextDeveloper\Accounting\Http\Controllers\Invoices;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\Invoices\InvoicesUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\InvoicesQueryFilter;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Services\InvoicesService;
use NextDeveloper\Accounting\Http\Requests\Invoices\InvoicesCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags;use NextDeveloper\Commons\Http\Traits\Addresses;
class InvoicesController extends AbstractController
{
    private $model = Invoices::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of invoices.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  InvoicesQueryFilter $filter  An object that builds search query
     * @param  Request             $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(InvoicesQueryFilter $filter, Request $request)
    {
        $data = InvoicesService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $invoicesId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = InvoicesService::getByRef($ref);

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
        $objects = InvoicesService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created Invoices object on database.
     *
     * @param  InvoicesCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(InvoicesCreateRequest $request)
    {
        $model = InvoicesService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Invoices object on database.
     *
     * @param  $invoicesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($invoicesId, InvoicesUpdateRequest $request)
    {
        $model = InvoicesService::update($invoicesId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates Invoices object on database.
     *
     * @param  $invoicesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($invoicesId)
    {
        $model = InvoicesService::delete($invoicesId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

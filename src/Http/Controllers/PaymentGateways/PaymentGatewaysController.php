<?php

namespace NextDeveloper\Accounting\Http\Controllers\PaymentGateways;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\PaymentGateways\PaymentGatewaysUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\PaymentGatewaysQueryFilter;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Services\PaymentGatewaysService;
use NextDeveloper\Accounting\Http\Requests\PaymentGateways\PaymentGatewaysCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags;use NextDeveloper\Commons\Http\Traits\Addresses;
class PaymentGatewaysController extends AbstractController
{
    private $model = PaymentGateways::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of paymentgateways.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PaymentGatewaysQueryFilter $filter  An object that builds search query
     * @param  Request                    $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PaymentGatewaysQueryFilter $filter, Request $request)
    {
        $data = PaymentGatewaysService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $paymentGatewaysId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PaymentGatewaysService::getByRef($ref);

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
        $objects = PaymentGatewaysService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created PaymentGateways object on database.
     *
     * @param  PaymentGatewaysCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PaymentGatewaysCreateRequest $request)
    {
        $model = PaymentGatewaysService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PaymentGateways object on database.
     *
     * @param  $paymentGatewaysId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($paymentGatewaysId, PaymentGatewaysUpdateRequest $request)
    {
        $model = PaymentGatewaysService::update($paymentGatewaysId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PaymentGateways object on database.
     *
     * @param  $paymentGatewaysId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($paymentGatewaysId)
    {
        $model = PaymentGatewaysService::delete($paymentGatewaysId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

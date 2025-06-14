<?php

namespace NextDeveloper\Accounting\Http\Controllers\PaymentCheckoutSessions;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\PaymentCheckoutSessionsQueryFilter;
use NextDeveloper\Accounting\Database\Models\PaymentCheckoutSessions;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Accounting\Http\Requests\PaymentCheckoutSessions\PaymentCheckoutSessionsCreateRequest;
use NextDeveloper\Accounting\Http\Requests\PaymentCheckoutSessions\PaymentCheckoutSessionsUpdateRequest;
use NextDeveloper\Accounting\Services\PaymentCheckoutSessionsService;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;

class PaymentCheckoutSessionsController extends AbstractController
{
    private $model = PaymentCheckoutSessions::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of paymentcheckoutsessions.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PaymentCheckoutSessionsQueryFilter $filter  An object that builds search query
     * @param  Request                            $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PaymentCheckoutSessionsQueryFilter $filter, Request $request)
    {
        $data = PaymentCheckoutSessionsService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = PaymentCheckoutSessionsService::getActions();

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
        $actionId = PaymentCheckoutSessionsService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $paymentCheckoutSessionsId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PaymentCheckoutSessionsService::getByRef($ref);

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
        $objects = PaymentCheckoutSessionsService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created PaymentCheckoutSessions object on database.
     *
     * @param  PaymentCheckoutSessionsCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PaymentCheckoutSessionsCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PaymentCheckoutSessionsService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PaymentCheckoutSessions object on database.
     *
     * @param  $paymentCheckoutSessionsId
     * @param  PaymentCheckoutSessionsUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($paymentCheckoutSessionsId, PaymentCheckoutSessionsUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = PaymentCheckoutSessionsService::update($paymentCheckoutSessionsId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PaymentCheckoutSessions object on database.
     *
     * @param  $paymentCheckoutSessionsId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($paymentCheckoutSessionsId)
    {
        $model = PaymentCheckoutSessionsService::delete($paymentCheckoutSessionsId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

<?php

namespace NextDeveloper\Accounting\Http\Controllers\PaymentGatewayMessages;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\PaymentGatewayMessages\PaymentGatewayMessagesUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\PaymentGatewayMessagesQueryFilter;
use NextDeveloper\Accounting\Database\Models\PaymentGatewayMessages;
use NextDeveloper\Accounting\Services\PaymentGatewayMessagesService;
use NextDeveloper\Accounting\Http\Requests\PaymentGatewayMessages\PaymentGatewayMessagesCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags;use NextDeveloper\Commons\Http\Traits\Addresses;
class PaymentGatewayMessagesController extends AbstractController
{
    private $model = PaymentGatewayMessages::class;

    use Tags;
    use Addresses;
    /**
     * This method returns the list of paymentgatewaymessages.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  PaymentGatewayMessagesQueryFilter $filter  An object that builds search query
     * @param  Request                           $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(PaymentGatewayMessagesQueryFilter $filter, Request $request)
    {
        $data = PaymentGatewayMessagesService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $actions = PaymentGatewayMessagesService::getActions();

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
        $actionId = PaymentGatewayMessagesService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $paymentGatewayMessagesId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = PaymentGatewayMessagesService::getByRef($ref);

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
        $objects = PaymentGatewayMessagesService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created PaymentGatewayMessages object on database.
     *
     * @param  PaymentGatewayMessagesCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(PaymentGatewayMessagesCreateRequest $request)
    {
        $model = PaymentGatewayMessagesService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PaymentGatewayMessages object on database.
     *
     * @param  $paymentGatewayMessagesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($paymentGatewayMessagesId, PaymentGatewayMessagesUpdateRequest $request)
    {
        $model = PaymentGatewayMessagesService::update($paymentGatewayMessagesId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates PaymentGatewayMessages object on database.
     *
     * @param  $paymentGatewayMessagesId
     * @param  CountryCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($paymentGatewayMessagesId)
    {
        $model = PaymentGatewayMessagesService::delete($paymentGatewayMessagesId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

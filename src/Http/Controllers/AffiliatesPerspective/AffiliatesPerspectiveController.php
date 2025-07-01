<?php

namespace NextDeveloper\Accounting\Http\Controllers\AffiliatesPerspective;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Accounting\Http\Requests\AffiliatesPerspective\AffiliatesPerspectiveUpdateRequest;
use NextDeveloper\Accounting\Database\Filters\AffiliatesPerspectiveQueryFilter;
use NextDeveloper\Accounting\Database\Models\AffiliatesPerspective;
use NextDeveloper\Accounting\Services\AffiliatesPerspectiveService;
use NextDeveloper\Accounting\Http\Requests\AffiliatesPerspective\AffiliatesPerspectiveCreateRequest;
use NextDeveloper\Commons\Http\Traits\Tags as TagsTrait;use NextDeveloper\Commons\Http\Traits\Addresses as AddressesTrait;
class AffiliatesPerspectiveController extends AbstractController
{
    private $model = AffiliatesPerspective::class;

    use TagsTrait;
    use AddressesTrait;
    /**
     * This method returns the list of affiliatesperspectives.
     *
     * optional http params:
     * - paginate: If you set paginate parameter, the result will be returned paginated.
     *
     * @param  AffiliatesPerspectiveQueryFilter $filter  An object that builds search query
     * @param  Request                          $request Laravel request object, this holds all data about request. Automatically populated.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(AffiliatesPerspectiveQueryFilter $filter, Request $request)
    {
        $data = AffiliatesPerspectiveService::get($filter, $request->all());

        return ResponsableFactory::makeResponse($this, $data);
    }

    /**
     * This function returns the list of actions that can be performed on this object.
     *
     * @return void
     */
    public function getActions()
    {
        $data = AffiliatesPerspectiveService::getActions();

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
        $actionId = AffiliatesPerspectiveService::doAction($objectId, $action, request()->all());

        return $this->withArray(
            [
            'action_id' =>  $actionId
            ]
        );
    }

    /**
     * This method receives ID for the related model and returns the item to the client.
     *
     * @param  $affiliatesPerspectiveId
     * @return mixed|null
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public function show($ref)
    {
        //  Here we are not using Laravel Route Model Binding. Please check routeBinding.md file
        //  in NextDeveloper Platform Project
        $model = AffiliatesPerspectiveService::getByRef($ref);

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
        $objects = AffiliatesPerspectiveService::relatedObjects($ref, $subObject);

        return ResponsableFactory::makeResponse($this, $objects);
    }

    /**
     * This method created AffiliatesPerspective object on database.
     *
     * @param  AffiliatesPerspectiveCreateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function store(AffiliatesPerspectiveCreateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = AffiliatesPerspectiveService::create($request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates AffiliatesPerspective object on database.
     *
     * @param  $affiliatesPerspectiveId
     * @param  AffiliatesPerspectiveUpdateRequest $request
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function update($affiliatesPerspectiveId, AffiliatesPerspectiveUpdateRequest $request)
    {
        if($request->has('validateOnly') && $request->get('validateOnly') == true) {
            return [
                'validation'    =>  'success'
            ];
        }

        $model = AffiliatesPerspectiveService::update($affiliatesPerspectiveId, $request->validated());

        return ResponsableFactory::makeResponse($this, $model);
    }

    /**
     * This method updates AffiliatesPerspective object on database.
     *
     * @param  $affiliatesPerspectiveId
     * @return mixed|null
     * @throws \NextDeveloper\Commons\Exceptions\CannotCreateModelException
     */
    public function destroy($affiliatesPerspectiveId)
    {
        $model = AffiliatesPerspectiveService::delete($affiliatesPerspectiveId);

        return $this->noContent();
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

<?php

namespace NextDeveloper\Accounting\Services\AbstractServices;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Helpers\DatabaseHelper;
use NextDeveloper\Commons\Database\Models\AvailableActions;
use NextDeveloper\Accounting\Database\Models\SalesPartnersPerspective;
use NextDeveloper\Accounting\Database\Filters\SalesPartnersPerspectiveQueryFilter;
use NextDeveloper\Commons\Exceptions\ModelNotFoundException;
use NextDeveloper\Events\Services\Events;
use NextDeveloper\Commons\Exceptions\NotAllowedException;

/**
 * This class is responsible from managing the data for SalesPartnersPerspective
 *
 * Class SalesPartnersPerspectiveService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class AbstractSalesPartnersPerspectiveService
{
    public static function get(SalesPartnersPerspectiveQueryFilter $filter = null, array $params = []) : Collection|LengthAwarePaginator
    {
        $enablePaginate = array_key_exists('paginate', $params);

        $request = new Request();

        /**
        * Here we are adding null request since if filter is null, this means that this function is called from
        * non http application. This is actually not I think its a correct way to handle this problem but it's a workaround.
        *
        * Please let me know if you have any other idea about this; baris.bulut@nextdeveloper.com
        */
        if($filter == null) {
            $filter = new SalesPartnersPerspectiveQueryFilter($request);
        }

        $perPage = config('commons.pagination.per_page');

        if($perPage == null) {
            $perPage = 20;
        }

        if(array_key_exists('per_page', $params)) {
            $perPage = intval($params['per_page']);

            if($perPage == 0) {
                $perPage = 20;
            }
        }

        if(array_key_exists('orderBy', $params)) {
            $filter->orderBy($params['orderBy']);
        }

        $model = SalesPartnersPerspective::filter($filter);

        if($enablePaginate) {
            //  We are using this because we have been experiencing huge security problem when we use the paginate method.
            //  The reason was, when the pagination method was using, somehow paginate was discarding all the filters.
            $modelCount = $model->count();
            $page = array_key_exists('page', $params) ? $params['page'] : 1;
            $items = $model->skip(($page - 1) * $perPage)->take($perPage)->get();

            return new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $modelCount,
                $perPage,
                $page
            );
        }

        return $model->get();
    }

    public static function getAll()
    {
        return SalesPartnersPerspective::all();
    }

    /**
     * This method returns the model by looking at reference id
     *
     * @param  $ref
     * @return mixed
     */
    public static function getByRef($ref) : ?SalesPartnersPerspective
    {
        return SalesPartnersPerspective::findByRef($ref);
    }

    public static function getActions()
    {
        $model = SalesPartnersPerspective::class;

        $model = Str::remove('Database\\Models\\', $model);

        $actions = AvailableActions::where('input', $model)
            ->get();

        return $actions;
    }

    /**
     * This method initiates the related action with the given parameters.
     */
    public static function doAction($objectId, $action, ...$params)
    {
        $object = SalesPartnersPerspective::where('uuid', $objectId)->first();

        $action = AvailableActions::where('name', $action)
            ->where('input', 'NextDeveloper\Accounting\SalesPartnersPerspective')
            ->first();

        $class = $action->class;

        if(class_exists($class)) {
            $action = new $class($object, $params);
            $actionId = $action->getActionId();

            if(request()->get('fg') == 'true') {
                $action->handle();
                return $actionId;
            }

            dispatch($action);

            return $actionId;
        }

        return null;
    }

    /**
     * This method returns the model by lookint at its id
     *
     * @param  $id
     * @return SalesPartnersPerspective|null
     */
    public static function getById($id) : ?SalesPartnersPerspective
    {
        return SalesPartnersPerspective::where('id', $id)->first();
    }

    /**
     * This method returns the sub objects of the related models
     *
     * @param  $uuid
     * @param  $object
     * @return void
     * @throws \Laravel\Octane\Exceptions\DdException
     */
    public static function relatedObjects($uuid, $object)
    {
        try {
            $obj = SalesPartnersPerspective::where('uuid', $uuid)->first();

            if(!$obj) {
                throw new ModelNotFoundException('Cannot find the related model');
            }

            if($obj) {
                return $obj->$object;
            }
        } catch (\Exception $e) {
            dd($e);
        }
    }

    /**
     * This method created the model from an array.
     *
     * Throws an exception if stuck with any problem.
     *
     * @param  array $data
     * @return mixed
     * @throw  Exception
     */
    public static function create(array $data)
    {
        if (array_key_exists('common_country_id', $data)) {
            $data['common_country_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Countries',
                $data['common_country_id']
            );
        }
        if (array_key_exists('common_domain_id', $data)) {
            $data['common_domain_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Domains',
                $data['common_domain_id']
            );
        }
        if (array_key_exists('iam_user_id', $data)) {
            $data['iam_user_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\IAM\Database\Models\Users',
                $data['iam_user_id']
            );
        }
                    
        if(!array_key_exists('iam_user_id', $data)) {
            $data['iam_user_id']    = UserHelper::me()->id;
        }
        if (array_key_exists('iam_account_type_id', $data)) {
            $data['iam_account_type_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\IAM\Database\Models\AccountTypes',
                $data['iam_account_type_id']
            );
        }
        if (array_key_exists('iam_account_id', $data)) {
            $data['iam_account_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\IAM\Database\Models\Accounts',
                $data['iam_account_id']
            );
        }
            
        if(!array_key_exists('iam_account_id', $data)) {
            $data['iam_account_id'] = UserHelper::currentAccount()->id;
        }
        if (array_key_exists('common_currency_id', $data)) {
            $data['common_currency_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Currencies',
                $data['common_currency_id']
            );
        }
                        
        try {
            $model = SalesPartnersPerspective::create($data);
        } catch(\Exception $e) {
            throw $e;
        }

        return $model->fresh();
    }

    /**
     * This function expects the ID inside the object.
     *
     * @param  array $data
     * @return SalesPartnersPerspective
     */
    public static function updateRaw(array $data) : ?SalesPartnersPerspective
    {
        if(array_key_exists('id', $data)) {
            return self::update($data['id'], $data);
        }

        return null;
    }

    /**
     * This method updated the model from an array.
     *
     * Throws an exception if stuck with any problem.
     *
     * @param
     * @param  array $data
     * @return mixed
     * @throw  Exception
     */
    public static function update($id, array $data)
    {
        $model = SalesPartnersPerspective::where('uuid', $id)->first();

        if(!$model) {
            throw new NotAllowedException(
                'We cannot find the related object to update. ' .
                'Maybe you dont have the permission to update this object?'
            );
        }

        if (array_key_exists('common_country_id', $data)) {
            $data['common_country_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Countries',
                $data['common_country_id']
            );
        }
        if (array_key_exists('common_domain_id', $data)) {
            $data['common_domain_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Domains',
                $data['common_domain_id']
            );
        }
        if (array_key_exists('iam_user_id', $data)) {
            $data['iam_user_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\IAM\Database\Models\Users',
                $data['iam_user_id']
            );
        }
        if (array_key_exists('iam_account_type_id', $data)) {
            $data['iam_account_type_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\IAM\Database\Models\AccountTypes',
                $data['iam_account_type_id']
            );
        }
        if (array_key_exists('iam_account_id', $data)) {
            $data['iam_account_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\IAM\Database\Models\Accounts',
                $data['iam_account_id']
            );
        }
        if (array_key_exists('common_currency_id', $data)) {
            $data['common_currency_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Currencies',
                $data['common_currency_id']
            );
        }
    
        try {
            $isUpdated = $model->update($data);
            $model = $model->fresh();
        } catch(\Exception $e) {
            throw $e;
        }

        return $model->fresh();
    }

    /**
     * This method updated the model from an array.
     *
     * Throws an exception if stuck with any problem.
     *
     * @param
     * @param  array $data
     * @return mixed
     * @throw  Exception
     */
    public static function delete($id)
    {
        $model = SalesPartnersPerspective::where('uuid', $id)->first();

        if(!$model) {
            throw new NotAllowedException(
                'We cannot find the related object to delete. ' .
                'Maybe you dont have the permission to update this object?'
            );
        }

        try {
            $model = $model->delete();
        } catch(\Exception $e) {
            throw $e;
        }

        return $model;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

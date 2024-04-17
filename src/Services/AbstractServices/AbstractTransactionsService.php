<?php

namespace NextDeveloper\Accounting\Services\AbstractServices;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Commons\Helpers\DatabaseHelper;
use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Accounting\Database\Filters\TransactionsQueryFilter;
use NextDeveloper\Commons\Exceptions\ModelNotFoundException;
use NextDeveloper\Events\Services\Events;

/**
 * This class is responsible from managing the data for Transactions
 *
 * Class TransactionsService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class AbstractTransactionsService
{
    public static function get(TransactionsQueryFilter $filter = null, array $params = []) : Collection|LengthAwarePaginator
    {
        $enablePaginate = array_key_exists('paginate', $params);

        /**
        * Here we are adding null request since if filter is null, this means that this function is called from
        * non http application. This is actually not I think its a correct way to handle this problem but it's a workaround.
        *
        * Please let me know if you have any other idea about this; baris.bulut@nextdeveloper.com
        */
        if($filter == null) {
            $filter = new TransactionsQueryFilter(new Request());
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

        $model = Transactions::filter($filter);

        if($model && $enablePaginate) {
            return $model->paginate($perPage);
        } else {
            return $model->get();
        }
    }

    public static function getAll()
    {
        return Transactions::all();
    }

    /**
     * This method returns the model by looking at reference id
     *
     * @param  $ref
     * @return mixed
     */
    public static function getByRef($ref) : ?Transactions
    {
        return Transactions::findByRef($ref);
    }

    public static function getActions()
    {
        return config('accounting.actions');
    }

    /**
     * This method returns the model by lookint at its id
     *
     * @param  $id
     * @return Transactions|null
     */
    public static function getById($id) : ?Transactions
    {
        return Transactions::where('id', $id)->first();
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
            $obj = Transactions::where('uuid', $uuid)->first();

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
        if (array_key_exists('accounting_invoice_id', $data)) {
            $data['accounting_invoice_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Accounting\Database\Models\Invoices',
                $data['accounting_invoice_id']
            );
        }
        if (array_key_exists('common_currency_id', $data)) {
            $data['common_currency_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Currencies',
                $data['common_currency_id']
            );
        }
        if (array_key_exists('accounting_payment_gateway_id', $data)) {
            $data['accounting_payment_gateway_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Accounting\Database\Models\PaymentGateways',
                $data['accounting_payment_gateway_id']
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
        if (array_key_exists('accounting_account_id', $data)) {
            $data['accounting_account_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Accounting\Database\Models\Accounts',
                $data['accounting_account_id']
            );
        }
                        
        try {
            $model = Transactions::create($data);
        } catch(\Exception $e) {
            throw $e;
        }

        Events::fire('created:NextDeveloper\Accounting\Transactions', $model);

        return $model->fresh();
    }

    /**
     * This function expects the ID inside the object.
     *
     * @param  array $data
     * @return Transactions
     */
    public static function updateRaw(array $data) : ?Transactions
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
        $model = Transactions::where('uuid', $id)->first();

        if (array_key_exists('accounting_invoice_id', $data)) {
            $data['accounting_invoice_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Accounting\Database\Models\Invoices',
                $data['accounting_invoice_id']
            );
        }
        if (array_key_exists('common_currency_id', $data)) {
            $data['common_currency_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Commons\Database\Models\Currencies',
                $data['common_currency_id']
            );
        }
        if (array_key_exists('accounting_payment_gateway_id', $data)) {
            $data['accounting_payment_gateway_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Accounting\Database\Models\PaymentGateways',
                $data['accounting_payment_gateway_id']
            );
        }
        if (array_key_exists('iam_account_id', $data)) {
            $data['iam_account_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\IAM\Database\Models\Accounts',
                $data['iam_account_id']
            );
        }
        if (array_key_exists('accounting_account_id', $data)) {
            $data['accounting_account_id'] = DatabaseHelper::uuidToId(
                '\NextDeveloper\Accounting\Database\Models\Accounts',
                $data['accounting_account_id']
            );
        }
    
        Events::fire('updating:NextDeveloper\Accounting\Transactions', $model);

        try {
            $isUpdated = $model->update($data);
            $model = $model->fresh();
        } catch(\Exception $e) {
            throw $e;
        }

        Events::fire('updated:NextDeveloper\Accounting\Transactions', $model);

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
        $model = Transactions::where('uuid', $id)->first();

        Events::fire('deleted:NextDeveloper\Accounting\Transactions', $model);

        try {
            $model = $model->delete();
        } catch(\Exception $e) {
            throw $e;
        }

        return $model;
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}

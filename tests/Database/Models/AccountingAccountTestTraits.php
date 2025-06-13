<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Accounting\Database\Filters\AccountingAccountQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingAccountService;

trait AccountingAccountTestTraits
{
    public $http;

    /**
     *   Creating the Guzzle object
     */
    public function setupGuzzle()
    {
        $this->http = new Client(
            [
            'base_uri'  =>  '127.0.0.1:8000'
            ]
        );
    }

    /**
     *   Destroying the Guzzle object
     */
    public function destroyGuzzle()
    {
        $this->http = null;
    }

    public function test_http_accountingaccount_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingaccount',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingaccount_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingaccount', [
            'form_params'   =>  [
                'tax_office'  =>  'a',
                'tax_number'  =>  'a',
                'accounting_identifier'  =>  'a',
                'trade_office_number'  =>  'a',
                'trade_office'  =>  'a',
                'tr_mersis'  =>  'a',
                            ],
                ['http_errors' => false]
            ]
        );

        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
    }

    /**
     * Get test
     *
     * @return bool
     */
    public function test_accountingaccount_model_get()
    {
        $result = AbstractAccountingAccountService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingaccount_get_all()
    {
        $result = AbstractAccountingAccountService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingaccount_get_paginated()
    {
        $result = AbstractAccountingAccountService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingaccount_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccount_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccount\AccountingAccountRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_tax_office_filter()
    {
        try {
            $request = new Request(
                [
                'tax_office'  =>  'a'
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_tax_number_filter()
    {
        try {
            $request = new Request(
                [
                'tax_number'  =>  'a'
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_accounting_identifier_filter()
    {
        try {
            $request = new Request(
                [
                'accounting_identifier'  =>  'a'
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_trade_office_number_filter()
    {
        try {
            $request = new Request(
                [
                'trade_office_number'  =>  'a'
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_trade_office_filter()
    {
        try {
            $request = new Request(
                [
                'trade_office'  =>  'a'
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_tr_mersis_filter()
    {
        try {
            $request = new Request(
                [
                'tr_mersis'  =>  'a'
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccount_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccount::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

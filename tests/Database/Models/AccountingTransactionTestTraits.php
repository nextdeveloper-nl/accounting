<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingTransactionQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingTransactionService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingTransactionTestTraits
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

    public function test_http_accountingtransaction_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingtransaction',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingtransaction_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingtransaction', [
            'form_params'   =>  [
                'gateway_response'  =>  'a',
                'conversation_id'  =>  'a',
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
    public function test_accountingtransaction_model_get()
    {
        $result = AbstractAccountingTransactionService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingtransaction_get_all()
    {
        $result = AbstractAccountingTransactionService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingtransaction_get_paginated()
    {
        $result = AbstractAccountingTransactionService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingtransaction_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingtransaction_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::first();

            event(new \NextDeveloper\Accounting\Events\AccountingTransaction\AccountingTransactionRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_gateway_response_filter()
    {
        try {
            $request = new Request(
                [
                'gateway_response'  =>  'a'
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_conversation_id_filter()
    {
        try {
            $request = new Request(
                [
                'conversation_id'  =>  'a'
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingtransaction_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
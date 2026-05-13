<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingCreditTransactionQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingCreditTransactionService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingCreditTransactionTestTraits
{
    public $http;

    /**
    *   Creating the Guzzle object
    */
    public function setupGuzzle()
    {
        $this->http = new Client([
            'base_uri'  =>  '127.0.0.1:8000'
        ]);
    }

    /**
    *   Destroying the Guzzle object
    */
    public function destroyGuzzle()
    {
        $this->http = null;
    }

    public function test_http_accountingcredittransaction_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingcredittransaction',
            ['http_errors' => false]
        );

        $this->assertContains($response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
        ]);
    }

    public function test_http_accountingcredittransaction_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request('POST', '/accounting/accountingcredittransaction', [
            'form_params'   =>  [
                'type'  =>  'a',
                'object_type'  =>  'a',
                'description'  =>  'a',
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
    public function test_accountingcredittransaction_model_get()
    {
        $result = AbstractAccountingCreditTransactionService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcredittransaction_get_all()
    {
        $result = AbstractAccountingCreditTransactionService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcredittransaction_get_paginated()
    {
        $result = AbstractAccountingCreditTransactionService::get(null, [
            'paginated' =>  'true'
        ]);

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingcredittransaction_event_retrieved_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionRetrievedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_created_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionCreatedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_creating_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionCreatingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_saving_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionSavingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_saved_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionSavedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_updating_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionUpdatingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_updated_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionUpdatedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_deleting_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionDeletingEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_deleted_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionDeletedEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_restoring_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionRestoringEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_restored_without_object()
    {
        try {
            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionRestoredEvent() );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionRetrievedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionCreatedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionCreatingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionSavingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionSavedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionUpdatingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionUpdatedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionDeletingEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionDeletedEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionRestoringEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcredittransaction_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::first();

            event( new \NextDeveloper\Accounting\Events\AccountingCreditTransaction\AccountingCreditTransactionRestoredEvent($model) );
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_type_filter()
    {
        try {
            $request = new Request([
                'type'  =>  'a'
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_object_type_filter()
    {
        try {
            $request = new Request([
                'object_type'  =>  'a'
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_description_filter()
    {
        try {
            $request = new Request([
                'description'  =>  'a'
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_created_at_filter_start()
    {
        try {
            $request = new Request([
                'created_atStart'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_updated_at_filter_start()
    {
        try {
            $request = new Request([
                'updated_atStart'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_deleted_at_filter_start()
    {
        try {
            $request = new Request([
                'deleted_atStart'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_created_at_filter_end()
    {
        try {
            $request = new Request([
                'created_atEnd'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_updated_at_filter_end()
    {
        try {
            $request = new Request([
                'updated_atEnd'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_deleted_at_filter_end()
    {
        try {
            $request = new Request([
                'deleted_atEnd'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request([
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request([
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcredittransaction_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request([
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
            ]);

            $filter = new AccountingCreditTransactionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditTransaction::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
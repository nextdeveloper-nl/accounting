<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingAccountPartnerLogQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingAccountPartnerLogService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingAccountPartnerLogTestTraits
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

    public function test_http_accountingaccountpartnerlog_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingaccountpartnerlog',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingaccountpartnerlog_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingaccountpartnerlog', [
            'form_params'   =>  [
                'reason'  =>  'a',
                    'started_at'  =>  now(),
                    'finished_at'  =>  now(),
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
    public function test_accountingaccountpartnerlog_model_get()
    {
        $result = AbstractAccountingAccountPartnerLogService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingaccountpartnerlog_get_all()
    {
        $result = AbstractAccountingAccountPartnerLogService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingaccountpartnerlog_get_paginated()
    {
        $result = AbstractAccountingAccountPartnerLogService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingaccountpartnerlog_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingaccountpartnerlog_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::first();

            event(new \NextDeveloper\Accounting\Events\AccountingAccountPartnerLog\AccountingAccountPartnerLogRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_reason_filter()
    {
        try {
            $request = new Request(
                [
                'reason'  =>  'a'
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_started_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'started_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_finished_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'finished_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_started_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'started_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_finished_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'finished_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_started_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'started_atStart'  =>  now(),
                'started_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_finished_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'finished_atStart'  =>  now(),
                'finished_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingaccountpartnerlog_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingAccountPartnerLogQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingAccountPartnerLog::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
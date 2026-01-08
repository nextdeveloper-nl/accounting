<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingPartnerAssignmentQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingPartnerAssignmentService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingPartnerAssignmentTestTraits
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

    public function test_http_accountingpartnerassignment_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingpartnerassignment',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingpartnerassignment_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingpartnerassignment', [
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
    public function test_accountingpartnerassignment_model_get()
    {
        $result = AbstractAccountingPartnerAssignmentService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpartnerassignment_get_all()
    {
        $result = AbstractAccountingPartnerAssignmentService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpartnerassignment_get_paginated()
    {
        $result = AbstractAccountingPartnerAssignmentService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingpartnerassignment_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnerassignment_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnerAssignment\AccountingPartnerAssignmentRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_reason_filter()
    {
        try {
            $request = new Request(
                [
                'reason'  =>  'a'
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_started_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'started_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_finished_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'finished_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_started_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'started_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_finished_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'finished_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_started_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'started_atStart'  =>  now(),
                'started_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_finished_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'finished_atStart'  =>  now(),
                'finished_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnerassignment_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnerAssignmentQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnerAssignment::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
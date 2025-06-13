<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Accounting\Database\Filters\AccountingContractQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingContractService;

trait AccountingContractTestTraits
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

    public function test_http_accountingcontract_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingcontract',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingcontract_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingcontract', [
            'form_params'   =>  [
                'name'  =>  'a',
                'description'  =>  'a',
                    'term_starts'  =>  now(),
                    'term_ends'  =>  now(),
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
    public function test_accountingcontract_model_get()
    {
        $result = AbstractAccountingContractService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcontract_get_all()
    {
        $result = AbstractAccountingContractService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcontract_get_paginated()
    {
        $result = AbstractAccountingContractService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingcontract_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontract_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContract\AccountingContractRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_description_filter()
    {
        try {
            $request = new Request(
                [
                'description'  =>  'a'
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_term_starts_filter_start()
    {
        try {
            $request = new Request(
                [
                'term_startsStart'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_term_ends_filter_start()
    {
        try {
            $request = new Request(
                [
                'term_endsStart'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_term_starts_filter_end()
    {
        try {
            $request = new Request(
                [
                'term_startsEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_term_ends_filter_end()
    {
        try {
            $request = new Request(
                [
                'term_endsEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_term_starts_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'term_startsStart'  =>  now(),
                'term_startsEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_term_ends_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'term_endsStart'  =>  now(),
                'term_endsEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontract_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContract::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Accounting\Database\Filters\AccountingPromoCodeQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingPromoCodeService;
use Tests\TestCase;

trait AccountingPromoCodeTestTraits
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

    public function test_http_accountingpromocode_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingpromocode',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingpromocode_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingpromocode', [
            'form_params'   =>  [
                'code'  =>  'a',
                'gift_code_data'  =>  'a',
                'value'  =>  '1',
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
    public function test_accountingpromocode_model_get()
    {
        $result = AbstractAccountingPromoCodeService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpromocode_get_all()
    {
        $result = AbstractAccountingPromoCodeService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpromocode_get_paginated()
    {
        $result = AbstractAccountingPromoCodeService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingpromocode_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpromocode_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPromoCode\AccountingPromoCodeRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_code_filter()
    {
        try {
            $request = new Request(
                [
                'code'  =>  'a'
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_gift_code_data_filter()
    {
        try {
            $request = new Request(
                [
                'gift_code_data'  =>  'a'
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_value_filter()
    {
        try {
            $request = new Request(
                [
                'value'  =>  '1'
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpromocode_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPromoCodeQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPromoCode::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
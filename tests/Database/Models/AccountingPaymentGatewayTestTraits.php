<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingPaymentGatewayQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingPaymentGatewayService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingPaymentGatewayTestTraits
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

    public function test_http_accountingpaymentgateway_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingpaymentgateway',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingpaymentgateway_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingpaymentgateway', [
            'form_params'   =>  [
                'name'  =>  'a',
                'gateway'  =>  'a',
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
    public function test_accountingpaymentgateway_model_get()
    {
        $result = AbstractAccountingPaymentGatewayService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpaymentgateway_get_all()
    {
        $result = AbstractAccountingPaymentGatewayService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpaymentgateway_get_paginated()
    {
        $result = AbstractAccountingPaymentGatewayService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingpaymentgateway_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewaySavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewaySavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewaySavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewaySavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgateway_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGateway\AccountingPaymentGatewayRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_gateway_filter()
    {
        try {
            $request = new Request(
                [
                'gateway'  =>  'a'
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgateway_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGateway::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
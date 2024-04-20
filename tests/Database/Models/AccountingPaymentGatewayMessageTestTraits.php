<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingPaymentGatewayMessageQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingPaymentGatewayMessageService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingPaymentGatewayMessageTestTraits
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

    public function test_http_accountingpaymentgatewaymessage_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingpaymentgatewaymessage',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingpaymentgatewaymessage_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingpaymentgatewaymessage', [
            'form_params'   =>  [
                'message_identifier'  =>  'a',
                'message'  =>  'a',
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
    public function test_accountingpaymentgatewaymessage_model_get()
    {
        $result = AbstractAccountingPaymentGatewayMessageService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpaymentgatewaymessage_get_all()
    {
        $result = AbstractAccountingPaymentGatewayMessageService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpaymentgatewaymessage_get_paginated()
    {
        $result = AbstractAccountingPaymentGatewayMessageService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingpaymentgatewaymessage_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentgatewaymessage_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentGatewayMessage\AccountingPaymentGatewayMessageRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_message_identifier_filter()
    {
        try {
            $request = new Request(
                [
                'message_identifier'  =>  'a'
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_message_filter()
    {
        try {
            $request = new Request(
                [
                'message'  =>  'a'
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentgatewaymessage_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentGatewayMessageQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentGatewayMessage::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
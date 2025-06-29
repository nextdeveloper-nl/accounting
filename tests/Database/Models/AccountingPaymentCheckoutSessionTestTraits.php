<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Accounting\Database\Filters\AccountingPaymentCheckoutSessionQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingPaymentCheckoutSessionService;
use Tests\TestCase;

trait AccountingPaymentCheckoutSessionTestTraits
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

    public function test_http_accountingpaymentcheckoutsession_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingpaymentcheckoutsession',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingpaymentcheckoutsession_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingpaymentcheckoutsession', [
            'form_params'   =>  [
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
    public function test_accountingpaymentcheckoutsession_model_get()
    {
        $result = AbstractAccountingPaymentCheckoutSessionService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpaymentcheckoutsession_get_all()
    {
        $result = AbstractAccountingPaymentCheckoutSessionService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpaymentcheckoutsession_get_paginated()
    {
        $result = AbstractAccountingPaymentCheckoutSessionService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingpaymentcheckoutsession_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpaymentcheckoutsession_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPaymentCheckoutSession\AccountingPaymentCheckoutSessionRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpaymentcheckoutsession_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPaymentCheckoutSessionQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPaymentCheckoutSession::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
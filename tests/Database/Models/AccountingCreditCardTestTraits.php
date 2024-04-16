<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingCreditCardQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingCreditCardService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingCreditCardTestTraits
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

    public function test_http_accountingcreditcard_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingcreditcard',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingcreditcard_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingcreditcard', [
            'form_params'   =>  [
                'name'  =>  'a',
                'type'  =>  'a',
                'cc_holder_name'  =>  'a',
                'cc_number'  =>  'a',
                'cc_month'  =>  'a',
                'cc_year'  =>  'a',
                'cc_cvv'  =>  'a',
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
    public function test_accountingcreditcard_model_get()
    {
        $result = AbstractAccountingCreditCardService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcreditcard_get_all()
    {
        $result = AbstractAccountingCreditCardService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcreditcard_get_paginated()
    {
        $result = AbstractAccountingCreditCardService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingcreditcard_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcreditcard_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::first();

            event(new \NextDeveloper\Accounting\Events\AccountingCreditCard\AccountingCreditCardRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_name_filter()
    {
        try {
            $request = new Request(
                [
                'name'  =>  'a'
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_type_filter()
    {
        try {
            $request = new Request(
                [
                'type'  =>  'a'
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_cc_holder_name_filter()
    {
        try {
            $request = new Request(
                [
                'cc_holder_name'  =>  'a'
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_cc_number_filter()
    {
        try {
            $request = new Request(
                [
                'cc_number'  =>  'a'
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_cc_month_filter()
    {
        try {
            $request = new Request(
                [
                'cc_month'  =>  'a'
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_cc_year_filter()
    {
        try {
            $request = new Request(
                [
                'cc_year'  =>  'a'
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_cc_cvv_filter()
    {
        try {
            $request = new Request(
                [
                'cc_cvv'  =>  'a'
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcreditcard_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingCreditCardQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingCreditCard::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
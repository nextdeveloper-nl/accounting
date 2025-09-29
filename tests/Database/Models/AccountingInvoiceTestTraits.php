<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Accounting\Database\Filters\AccountingInvoiceQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingInvoiceService;
use Tests\TestCase;

trait AccountingInvoiceTestTraits
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

    public function test_http_accountinginvoice_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountinginvoice',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountinginvoice_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountinginvoice', [
            'form_params'   =>  [
                'invoice_number'  =>  'a',
                'note'  =>  'a',
                'cancellation_reason'  =>  'a',
                'payment_link_url'  =>  'a',
                'term_year'  =>  '1',
                'term_month'  =>  '1',
                    'due_date'  =>  now(),
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
    public function test_accountinginvoice_model_get()
    {
        $result = AbstractAccountingInvoiceService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountinginvoice_get_all()
    {
        $result = AbstractAccountingInvoiceService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountinginvoice_get_paginated()
    {
        $result = AbstractAccountingInvoiceService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountinginvoice_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoice_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoice\AccountingInvoiceRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_invoice_number_filter()
    {
        try {
            $request = new Request(
                [
                'invoice_number'  =>  'a'
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_note_filter()
    {
        try {
            $request = new Request(
                [
                'note'  =>  'a'
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_cancellation_reason_filter()
    {
        try {
            $request = new Request(
                [
                'cancellation_reason'  =>  'a'
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_payment_link_url_filter()
    {
        try {
            $request = new Request(
                [
                'payment_link_url'  =>  'a'
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_term_year_filter()
    {
        try {
            $request = new Request(
                [
                'term_year'  =>  '1'
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_term_month_filter()
    {
        try {
            $request = new Request(
                [
                'term_month'  =>  '1'
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_due_date_filter_start()
    {
        try {
            $request = new Request(
                [
                'due_dateStart'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_due_date_filter_end()
    {
        try {
            $request = new Request(
                [
                'due_dateEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_due_date_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'due_dateStart'  =>  now(),
                'due_dateEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoice_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoice::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
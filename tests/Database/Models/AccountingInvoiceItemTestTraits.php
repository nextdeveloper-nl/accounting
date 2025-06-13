<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Accounting\Database\Filters\AccountingInvoiceItemQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingInvoiceItemService;

trait AccountingInvoiceItemTestTraits
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

    public function test_http_accountinginvoiceitem_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountinginvoiceitem',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountinginvoiceitem_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountinginvoiceitem', [
            'form_params'   =>  [
                'object_type'  =>  'a',
                'quantity'  =>  '1',
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
    public function test_accountinginvoiceitem_model_get()
    {
        $result = AbstractAccountingInvoiceItemService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountinginvoiceitem_get_all()
    {
        $result = AbstractAccountingInvoiceItemService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountinginvoiceitem_get_paginated()
    {
        $result = AbstractAccountingInvoiceItemService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountinginvoiceitem_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountinginvoiceitem_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingInvoiceItem\AccountingInvoiceItemRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_object_type_filter()
    {
        try {
            $request = new Request(
                [
                'object_type'  =>  'a'
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_quantity_filter()
    {
        try {
            $request = new Request(
                [
                'quantity'  =>  '1'
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountinginvoiceitem_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingInvoiceItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingInvoiceItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

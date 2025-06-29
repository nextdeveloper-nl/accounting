<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;
use NextDeveloper\Accounting\Database\Filters\AccountingContractItemQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingContractItemService;
use Tests\TestCase;

trait AccountingContractItemTestTraits
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

    public function test_http_accountingcontractitem_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingcontractitem',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingcontractitem_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingcontractitem', [
            'form_params'   =>  [
                'object_type'  =>  'a',
                'contract_type'  =>  'a',
                'discount'  =>  '1',
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
    public function test_accountingcontractitem_model_get()
    {
        $result = AbstractAccountingContractItemService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcontractitem_get_all()
    {
        $result = AbstractAccountingContractItemService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingcontractitem_get_paginated()
    {
        $result = AbstractAccountingContractItemService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingcontractitem_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingcontractitem_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::first();

            event(new \NextDeveloper\Accounting\Events\AccountingContractItem\AccountingContractItemRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_object_type_filter()
    {
        try {
            $request = new Request(
                [
                'object_type'  =>  'a'
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_contract_type_filter()
    {
        try {
            $request = new Request(
                [
                'contract_type'  =>  'a'
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_discount_filter()
    {
        try {
            $request = new Request(
                [
                'discount'  =>  '1'
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingcontractitem_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingContractItemQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingContractItem::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

}
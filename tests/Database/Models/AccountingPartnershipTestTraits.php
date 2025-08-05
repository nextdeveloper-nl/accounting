<?php

namespace NextDeveloper\Accounting\Tests\Database\Models;

use Tests\TestCase;
use GuzzleHttp\Client;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\AccountingPartnershipQueryFilter;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountingPartnershipService;
use Illuminate\Pagination\LengthAwarePaginator;
use League\Fractal\Resource\Collection;

trait AccountingPartnershipTestTraits
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

    public function test_http_accountingpartnership_get()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'GET',
            '/accounting/accountingpartnership',
            ['http_errors' => false]
        );

        $this->assertContains(
            $response->getStatusCode(), [
            Response::HTTP_OK,
            Response::HTTP_NOT_FOUND
            ]
        );
    }

    public function test_http_accountingpartnership_post()
    {
        $this->setupGuzzle();
        $response = $this->http->request(
            'POST', '/accounting/accountingpartnership', [
            'form_params'   =>  [
                'partner_code'  =>  'a',
                'industry'  =>  'a',
                'meeting_link'  =>  'a',
                'customer_count'  =>  '1',
                'level'  =>  '1',
                'reward_points'  =>  '1',
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
    public function test_accountingpartnership_model_get()
    {
        $result = AbstractAccountingPartnershipService::get();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpartnership_get_all()
    {
        $result = AbstractAccountingPartnershipService::getAll();

        $this->assertIsObject($result, Collection::class);
    }

    public function test_accountingpartnership_get_paginated()
    {
        $result = AbstractAccountingPartnershipService::get(
            null, [
            'paginated' =>  'true'
            ]
        );

        $this->assertIsObject($result, LengthAwarePaginator::class);
    }

    public function test_accountingpartnership_event_retrieved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipRetrievedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_created_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipCreatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_creating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipCreatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_saving_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipSavingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_saved_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipSavedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_updating_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipUpdatingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_updated_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipUpdatedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_deleting_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipDeletingEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_deleted_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipDeletedEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_restoring_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipRestoringEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_restored_without_object()
    {
        try {
            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipRestoredEvent());
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_retrieved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipRetrievedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_created_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipCreatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_creating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipCreatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_saving_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipSavingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_saved_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipSavedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_updating_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipUpdatingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_updated_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipUpdatedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_deleting_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipDeletingEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_deleted_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipDeletedEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_restoring_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipRestoringEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    public function test_accountingpartnership_event_restored_with_object()
    {
        try {
            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::first();

            event(new \NextDeveloper\Accounting\Events\AccountingPartnership\AccountingPartnershipRestoredEvent($model));
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_partner_code_filter()
    {
        try {
            $request = new Request(
                [
                'partner_code'  =>  'a'
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_industry_filter()
    {
        try {
            $request = new Request(
                [
                'industry'  =>  'a'
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_meeting_link_filter()
    {
        try {
            $request = new Request(
                [
                'meeting_link'  =>  'a'
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_customer_count_filter()
    {
        try {
            $request = new Request(
                [
                'customer_count'  =>  '1'
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_level_filter()
    {
        try {
            $request = new Request(
                [
                'level'  =>  '1'
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_reward_points_filter()
    {
        try {
            $request = new Request(
                [
                'reward_points'  =>  '1'
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_created_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_updated_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_deleted_at_filter_start()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_created_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_updated_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_deleted_at_filter_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_created_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'created_atStart'  =>  now(),
                'created_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_updated_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'updated_atStart'  =>  now(),
                'updated_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }

    public function test_accountingpartnership_event_deleted_at_filter_start_and_end()
    {
        try {
            $request = new Request(
                [
                'deleted_atStart'  =>  now(),
                'deleted_atEnd'  =>  now()
                ]
            );

            $filter = new AccountingPartnershipQueryFilter($request);

            $model = \NextDeveloper\Accounting\Database\Models\AccountingPartnership::filter($filter)->first();
        } catch (\Exception $e) {
            $this->assertFalse(false, $e->getMessage());
        }

        $this->assertTrue(true);
    }
    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}
<?php

namespace NextDeveloper\Accounting\Http\Controllers\PaymentGateways;

use Illuminate\Http\Request;
use NextDeveloper\Accounting\Database\Filters\PaymentGatewaysQueryFilter;
use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Accounting\Http\Controllers\AbstractController;
use NextDeveloper\Accounting\Http\Requests\PaymentGateways\PaymentGatewaysCreateRequest;
use NextDeveloper\Accounting\Http\Requests\PaymentGateways\PaymentGatewaysUpdateRequest;
use NextDeveloper\Accounting\Services\PaymentGatewaysService;
use NextDeveloper\Commons\Http\Response\ResponsableFactory;
use NextDeveloper\Commons\Http\Traits\Addresses;
use NextDeveloper\Commons\Http\Traits\Tags;

class PaymentGatewayWebhooksController extends AbstractController
{
    private $model = PaymentGateways::class;

    use Tags;
    use Addresses;

    public function stripe($event, Request $request)
    {
        logger()->debug($event);
        logger()->debug($request->all());
    }
}

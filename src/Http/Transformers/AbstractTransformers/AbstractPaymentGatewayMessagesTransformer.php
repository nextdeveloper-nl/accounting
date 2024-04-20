<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Accounting\Database\Models\PaymentGatewayMessages;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class PaymentGatewayMessagesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractPaymentGatewayMessagesTransformer extends AbstractTransformer
{

    /**
     * @param PaymentGatewayMessages $model
     *
     * @return array
     */
    public function transform(PaymentGatewayMessages $model)
    {
                        $accountingPaymentGatewayId = \NextDeveloper\Accounting\Database\Models\PaymentGateways::where('id', $model->accounting_payment_gateway_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'message_identifier'  =>  $model->message_identifier,
            'message'  =>  $model->message,
            'accounting_payment_gateway_id'  =>  $accountingPaymentGatewayId ? $accountingPaymentGatewayId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE





}

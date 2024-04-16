<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Accounting\Database\Models\PaymentGateways;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class PaymentGatewaysTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractPaymentGatewaysTransformer extends AbstractTransformer
{

    /**
     * @param PaymentGateways $model
     *
     * @return array
     */
    public function transform(PaymentGateways $model)
    {
                        $commonCountryId = \NextDeveloper\Commons\Database\Models\Countries::where('id', $model->common_country_id)->first();
                    $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'name'  =>  $model->name,
            'gateway'  =>  $model->gateway,
            'is_active'  =>  $model->is_active,
            'common_country_id'  =>  $commonCountryId ? $commonCountryId->uuid : null,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            'parameters'  =>  $model->parameters,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE










}

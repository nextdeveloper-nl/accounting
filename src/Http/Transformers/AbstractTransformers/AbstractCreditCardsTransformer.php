<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Accounting\Database\Models\CreditCards;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class CreditCardsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractCreditCardsTransformer extends AbstractTransformer
{

    /**
     * @param CreditCards $model
     *
     * @return array
     */
    public function transform(CreditCards $model)
    {
                        $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
                    $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'name'  =>  $model->name,
            'type'  =>  $model->type,
            'cc_holder_name'  =>  $model->cc_holder_name,
            'cc_number'  =>  $model->cc_number,
            'cc_month'  =>  $model->cc_month,
            'cc_year'  =>  $model->cc_year,
            'cc_cvv'  =>  $model->cc_cvv,
            'is_default'  =>  $model->is_default,
            'is_valid'  =>  $model->is_valid,
            'is_active'  =>  $model->is_active,
            'is_3d_secure'  =>  $model->is_3d_secure,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE


}

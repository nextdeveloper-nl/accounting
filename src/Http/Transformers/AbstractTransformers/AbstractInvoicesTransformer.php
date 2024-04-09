<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class InvoicesTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractInvoicesTransformer extends AbstractTransformer
{

    /**
     * @param Invoices $model
     *
     * @return array
     */
    public function transform(Invoices $model)
    {
                        $accountingAccountId = \NextDeveloper\Accounting\Database\Models\Accounts::where('id', $model->accounting_account_id)->first();
                    $commonCurrencyId = \NextDeveloper\Commons\Database\Models\Currencies::where('id', $model->common_currency_id)->first();
                    $giftCodeId = \NextDeveloper\\Database\Models\GiftCodes::where('id', $model->gift_code_id)->first();
                    $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
                    $iamUserId = \NextDeveloper\IAM\Database\Models\Users::where('id', $model->iam_user_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'accounting_account_id'  =>  $accountingAccountId ? $accountingAccountId->uuid : null,
            'invoice_number'  =>  $model->invoice_number,
            'exchange_rate'  =>  $model->exchange_rate,
            'amount'  =>  $model->amount,
            'common_currency_id'  =>  $commonCurrencyId ? $commonCurrencyId->uuid : null,
            'vat'  =>  $model->vat,
            'is_paid'  =>  $model->is_paid,
            'is_refund'  =>  $model->is_refund,
            'due_date'  =>  $model->due_date,
            'gift_code_id'  =>  $giftCodeId ? $giftCodeId->uuid : null,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'iam_user_id'  =>  $iamUserId ? $iamUserId->uuid : null,
            'is_payable'  =>  $model->is_payable,
            'is_sealed'  =>  $model->is_sealed,
            'note'  =>  $model->note,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE






}

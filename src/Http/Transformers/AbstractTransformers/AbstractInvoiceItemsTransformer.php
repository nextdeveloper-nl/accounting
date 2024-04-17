<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Accounting\Database\Models\InvoiceItems;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class InvoiceItemsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractInvoiceItemsTransformer extends AbstractTransformer
{

    /**
     * @param InvoiceItems $model
     *
     * @return array
     */
    public function transform(InvoiceItems $model)
    {
                        $commonCurrencyId = \NextDeveloper\Commons\Database\Models\Currencies::where('id', $model->common_currency_id)->first();
                    $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
                    $accountingInvoiceId = \NextDeveloper\Accounting\Database\Models\Invoices::where('id', $model->accounting_invoice_id)->first();
                    $accountingPromoCodeId = \NextDeveloper\Accounting\Database\Models\PromoCodes::where('id', $model->accounting_promo_code_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'object_type'  =>  $model->object_type,
            'object_id'  =>  $model->object_id,
            'quantity'  =>  $model->quantity,
            'unit_price'  =>  $model->unit_price,
            'common_currency_id'  =>  $commonCurrencyId ? $commonCurrencyId->uuid : null,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'accounting_invoice_id'  =>  $accountingInvoiceId ? $accountingInvoiceId->uuid : null,
            'total_price'  =>  $model->total_price,
            'accounting_promo_code_id'  =>  $accountingPromoCodeId ? $accountingPromoCodeId->uuid : null,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE











}

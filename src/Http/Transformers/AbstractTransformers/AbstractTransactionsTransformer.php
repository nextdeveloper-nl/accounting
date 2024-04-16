<?php

namespace NextDeveloper\Accounting\Http\Transformers\AbstractTransformers;

use NextDeveloper\Accounting\Database\Models\Transactions;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;

/**
 * Class TransactionsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class AbstractTransactionsTransformer extends AbstractTransformer
{

    /**
     * @param Transactions $model
     *
     * @return array
     */
    public function transform(Transactions $model)
    {
                        $accountingInvoiceId = \NextDeveloper\Accounting\Database\Models\Invoices::where('id', $model->accounting_invoice_id)->first();
                    $commonCurrencyId = \NextDeveloper\Commons\Database\Models\Currencies::where('id', $model->common_currency_id)->first();
                    $accountingPaymentGatewayId = \NextDeveloper\Accounting\Database\Models\PaymentGateways::where('id', $model->accounting_payment_gateway_id)->first();
                    $iamAccountId = \NextDeveloper\IAM\Database\Models\Accounts::where('id', $model->iam_account_id)->first();
                    $accountingAccountId = \NextDeveloper\Accounting\Database\Models\Accounts::where('id', $model->accounting_account_id)->first();
                    $conversationId = \NextDeveloper\\Database\Models\Conversations::where('id', $model->conversation_id)->first();
        
        return $this->buildPayload(
            [
            'id'  =>  $model->uuid,
            'accounting_invoice_id'  =>  $accountingInvoiceId ? $accountingInvoiceId->uuid : null,
            'amount'  =>  $model->amount,
            'common_currency_id'  =>  $commonCurrencyId ? $commonCurrencyId->uuid : null,
            'accounting_payment_gateway_id'  =>  $accountingPaymentGatewayId ? $accountingPaymentGatewayId->uuid : null,
            'iam_account_id'  =>  $iamAccountId ? $iamAccountId->uuid : null,
            'accounting_account_id'  =>  $accountingAccountId ? $accountingAccountId->uuid : null,
            'gateway_response'  =>  $model->gateway_response,
            'created_at'  =>  $model->created_at,
            'updated_at'  =>  $model->updated_at,
            'deleted_at'  =>  $model->deleted_at,
            'conversation_id'  =>  $conversationId ? $conversationId->uuid : null,
            'is_pending'  =>  $model->is_pending,
            ]
        );
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE









}

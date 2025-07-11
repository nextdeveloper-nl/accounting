<?php

namespace NextDeveloper\Accounting\Actions\Invoices;

use Helpers\InvoiceHelper;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Events\Services\Events;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This action tries to charge the customer for the payment, and if it is successful, it will mark the invoice as paid.
 *
 * It will use the related payment gateway and payment method to charge the customer.
 */
class RecalculateInvoice extends AbstractAction
{
    public const EVENTS = [
        'calculating-invoice:NextDeveloper\Accounting\Invoices',
        'invoice-calculated:NextDeveloper\Accounting\Invoices'
    ];

    /**
     * @param Invoices $invoice
     */
    public function __construct(Invoices $invoice, $params = null, $previousAction = null)
    {
        $this->model = $invoice;

        $this->queue = 'accounting';

        UserHelper::setAdminAsCurrentUser();

        parent::__construct($params, $previousAction);
    }

    public function handle()
    {
        $this->setProgress(0, 'Starting to recalculate the invoice');

        Events::fire('calculating-invoice:NextDeveloper\Accounting\Invoices', $this->model);

        try {
            InvoiceHelper::updateInvoiceAmount($this->model);
        } catch (\Exception $e) {
            Log::error('[INVOICE-ERROR]' . $e->getMessage(), $e->getTrace());
            return;
        }

        Events::fire('invoice-calculated:NextDeveloper\Accounting\Invoices', $this->model);

        $this->setFinished('Invoice calculated');
    }
}

<?php

namespace NextDeveloper\Accounting\Actions\Invoices;

use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Services\InvoicesService;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Events\Services\Events;

/**
 * The class is responsible for creating payment link for the given invoice.
 *
 * This action will be triggered when a payment link needs to be generated for an invoice.
 */
class CreatePaymentLink extends AbstractAction
{

    public const EVENTS = [
        //
    ];

    /**
     * @param Invoices $invoice
     * @param null $params
     * @param null $previousAction
     * @throws NotAllowedException
     */
    public function __construct(Invoices $invoice, $params = null, $previousAction = null)
    {
        $this->model = $invoice;

        parent::__construct($params, $previousAction);
    }

    public function handle(): void
    {
        $this->setProgress(0, 'Creating payment link for invoice.');

        if ($this->model->is_paid) {
            $this->setFinishedWithError('Invoice is already paid. Payment link cannot be created.');
            return;
        }

        $this->setProgress(50, 'Generating payment link via payment gateway.');

        // Create payment link using the service
        $paymentLink = InvoicesService::createPaymentLink($this->model);

        if (!$paymentLink) {
            $this->setFinishedWithError('Failed to create payment link. Please check if payment gateway is configured properly.');
            return;
        }

        $this->setProgress(100, 'Payment link created successfully.');

        $this->setFinished('Payment link created: ' . $paymentLink);
    }
}

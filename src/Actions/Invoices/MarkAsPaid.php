<?php

namespace NextDeveloper\Accounting\Actions\Invoices;

use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Events\Services\Events;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This action marks the invoice as paid.
 *
 * It will use the related payment gateway and payment method to charge the customer.
 */
class MarkAsPaid extends AbstractAction
{
    private $conversationId = 0;

    public const EVENTS = [
        'marked-as-paid:NextDeveloper\Accounting\Invoices',
        'not-allowed:NextDeveloper\Accounting\Invoices',
    ];

    /**
     * @param Invoices $invoice
     */
    public function __construct(Invoices $invoice, $params = null, $previousAction = null)
    {
        $this->model = $invoice;

        parent::__construct($params, $previousAction);
    }

    public function handle()
    {
        $this->setProgress(0, 'Marking the invoice as paid.');

        if(!UserHelper::has('accounting-manager')) {
            $this->setFinishedWithError('You cannot set this invoice as paid because you are not an accounting manager.');
            Events::fire('not-allowed:NextDeveloper\Accounting\Invoices', $this->model);
            return;
        }

        $this->model->update([
            'is_paid'   =>  true
        ]);

        Events::fire('marked-as-paid:NextDeveloper\Accounting\Invoices', $this->model);

        $this->setFinished('Marked as paid.');
    }
}

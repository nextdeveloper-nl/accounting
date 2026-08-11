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
        'paid:NextDeveloper\Accounting\Invoices',
        'not-allowed:NextDeveloper\Accounting\Invoices',
    ];

    /**
     * Roles that may mark an invoice as paid by hand.
     *
     * UserHelper::has() is an exact name match with no role hierarchy, so every role that
     * should be able to do this has to be listed. accounting-admin is LEVEL 10 against
     * accounting-manager's LEVEL 20, meaning it is the more privileged of the two, and used
     * to be rejected here for holding the wrong name.
     */
    private const ALLOWED_ROLES = [
        'accounting-admin',
        'accounting-manager',
        'system-admin',
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

        if(!$this->isAllowed()) {
            $this->setFinishedWithError('You cannot set this invoice as paid because you do not have any of ' .
                'these roles: ' . implode(', ', self::ALLOWED_ROLES) . '.');
            Events::fire('not-allowed:NextDeveloper\Accounting\Invoices', $this->model);
            return;
        }

        if($this->model->is_paid) {
            //  Firing paid: a second time would re-run the post payment side effects, so we stop here.
            $this->setFinished('Invoice is already marked as paid.');
            return;
        }

        $this->model->update([
            'is_paid'   =>  true
        ]);

        Events::fire('marked-as-paid:NextDeveloper\Accounting\Invoices', $this->model);

        //  The post payment side effects (invoice paid notifications, signing the contract the
        //  invoice belongs to) are bound to paid:, not to marked-as-paid:. An invoice marked as
        //  paid by hand is paid as far as the rest of the system is concerned, so it fires here too.
        Events::fire('paid:NextDeveloper\Accounting\Invoices', $this->model);

        $this->setFinished('Marked as paid.');
    }

    private function isAllowed() : bool
    {
        foreach (self::ALLOWED_ROLES as $role) {
            if(UserHelper::has($role)) {
                return true;
            }
        }

        return false;
    }
}

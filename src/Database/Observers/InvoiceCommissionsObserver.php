<?php

namespace NextDeveloper\Accounting\Database\Observers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Events\Services\Events;

/**
 * Class InvoiceCommissionsObserver
 *
 * @package NextDeveloper\Accounting\Database\Observers
 */
class InvoiceCommissionsObserver
{
    /**
     * Triggered when a new record is retrieved.
     *
     * @param Model $model
     */
    public function retrieved(Model $model)
    {

    }

    public function creating(Model $model)
    {
        throw_if(
            !UserHelper::can('create', $model),
            new NotAllowedException('You are not allowed to create this record')
        );

        Events::fire('creating:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function created(Model $model)
    {
        Events::fire('created:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function saving(Model $model)
    {
        throw_if(
            !UserHelper::can('save', $model),
            new NotAllowedException('You are not allowed to save this record')
        );

        Events::fire('saving:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function saved(Model $model)
    {
        Events::fire('saved:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function updating(Model $model)
    {
        throw_if(
            !UserHelper::can('update', $model),
            new NotAllowedException('You are not allowed to update this record')
        );

        Events::fire('updating:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function updated(Model $model)
    {
        Events::fire('updated:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function deleting(Model $model)
    {
        throw_if(
            !UserHelper::can('delete', $model),
            new NotAllowedException('You are not allowed to delete this record')
        );

        Events::fire('deleting:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function deleted(Model $model)
    {
        Events::fire('deleted:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function restoring(Model $model)
    {
        throw_if(
            !UserHelper::can('restore', $model),
            new NotAllowedException('You are not allowed to restore this record')
        );

        Events::fire('restoring:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    public function restored(Model $model)
    {
        Events::fire('restored:NextDeveloper\\Accounting\\InvoiceCommissions', $model);
    }

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
}

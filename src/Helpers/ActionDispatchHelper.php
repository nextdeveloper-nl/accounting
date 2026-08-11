<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Support\Facades\Log;
use NextDeveloper\Commons\Actions\AbstractAction;
use NextDeveloper\Commons\Database\Models\ActionLogs;
use NextDeveloper\Commons\Helpers\ActionsHelper;

/**
 * Leaves a breadcrumb at the moment an action is handed over to the queue.
 *
 * AbstractAction creates its common_actions row at construction time, with progress = 0, and
 * only writes to common_action_logs once the worker actually starts running it. That makes a
 * job that was queued but never consumed - no worker on the connection, queue flushed on
 * deploy, worker killed by a hard fatal before it could report - look exactly like a job that
 * was never dispatched at all: progress = 0, zero log rows, nothing in failed_jobs.
 *
 * With this breadcrumb the two cases can be told apart: one log row and progress = 0 means the
 * job reached the queue and nothing picked it up; zero log rows means it never got that far.
 */
class ActionDispatchHelper
{
    /**
     * @param AbstractAction $action The action instance that is about to be dispatched
     * @param string         $class  The action class name, used for the file log
     */
    public static function markAsQueued($action, string $class) : void
    {
        $connection = config('queue.default');

        //  On the sync connection there is no queue to lose the job in, the dispatch call runs
        //  it inline, so a "queued" row would only add noise and would be out of order.
        if($connection === 'sync') {
            return;
        }

        $queue = $action->queue ?: config('queue.connections.' . $connection . '.queue');

        Log::info('[ActionLog][QUEUED] ' . $class . ' / connection: ' . $connection . ' / queue: ' . $queue);

        if(!ActionsHelper::saveInDb()) {
            return;
        }

        $actionModel = $action->getAction();

        if(!$actionModel) {
            return;
        }

        try {
            ActionLogs::create([
                'common_action_id'  =>  $actionModel->id,
                'log'               =>  'Action is queued on connection "' . $connection . '" / queue "' .
                    $queue . '". Waiting for a worker to pick it up.',
                'runtime'           =>  0,
                'iam_account_id'    =>  $actionModel->iam_account_id,
                'iam_user_id'       =>  $actionModel->iam_user_id,
            ]);
        } catch (\Exception $e) {
            //  A missing breadcrumb must never take the dispatch down with it.
            Log::error(__METHOD__ . ' | I cannot create the queued action log. And the reason is; ' . $e->getMessage());
        }
    }
}

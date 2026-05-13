<?php

namespace NextDeveloper\Accounting\Helpers;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\CreditTransactions;
use NextDeveloper\Accounting\Services\CreditTransactionsService;
use NextDeveloper\IAM\Helpers\UserHelper;

class CreditTransactionsHelper
{
    /**
     * Records a credit transaction for the given accounting account.
     *
     * The balance_after is read from the account after a fresh() so it reflects any
     * credit change that was applied before this log call.
     *
     * iam_account_id is always taken from the account itself so this method is safe
     * to call from background jobs where there is no active user session.
     *
     * @param Accounts    $account
     * @param float       $amount      Positive for credits, negative for debits/usage.
     * @param string      $type        Transaction type (e.g. 'credit', 'debit', 'usage', 'refund').
     * @param string|null $description Human-readable description.
     * @param string|null $objectType  Fully-qualified model class of the related object, if any.
     * @param int|null    $objectId    Integer PK of the related object, if any.
     */
    public static function log(
        Accounts $account,
        float $amount,
        string $type,
        ?string $description = null,
        ?string $objectType = null,
        ?int $objectId = null
    ): CreditTransactions {
        $account = $account->fresh();

        $data = [
            'accounting_account_id' => $account->id,
            'amount'                => $amount,
            'type'                  => $type,
            'balance_after'         => (float) $account->credit,
            'description'           => $description,
            'object_type'           => $objectType,
            'object_id'             => $objectId,
            'iam_account_id'        => $account->iam_account_id,
        ];

        try {
            $me = UserHelper::me();
            if ($me) {
                $data['iam_user_id'] = $me->id;
            }
        } catch (\Throwable) {
            // background job — no active session, leave iam_user_id unset
        }

        return CreditTransactionsService::create($data);
    }

    /**
     * Shorthand for logging a credit addition (e.g. top-up, refund).
     */
    public static function logCredit(
        Accounts $account,
        float $amount,
        ?string $description = null,
        ?string $objectType = null,
        ?int $objectId = null
    ): CreditTransactions {
        return self::log($account, $amount, 'credit', $description, $objectType, $objectId);
    }

    /**
     * Shorthand for logging a credit deduction (e.g. usage charge, penalty).
     */
    public static function logDebit(
        Accounts $account,
        float $amount,
        ?string $description = null,
        ?string $objectType = null,
        ?int $objectId = null
    ): CreditTransactions {
        return self::log($account, $amount, 'debit', $description, $objectType, $objectId);
    }

    /**
     * Shorthand for logging resource usage consumption.
     */
    public static function logUsage(
        Accounts $account,
        float $amount,
        ?string $description = null,
        ?string $objectType = null,
        ?int $objectId = null
    ): CreditTransactions {
        return self::log($account, $amount, 'usage', $description, $objectType, $objectId);
    }
}

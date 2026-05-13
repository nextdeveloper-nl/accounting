<?php

namespace NextDeveloper\Accounting\Helpers;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Exceptions\InsufficientCreditException;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class CreditHelper
{
    /**
     * Returns the credit balance of the given accounting account in USD.
     * If the account's currency is not USD, the stored value is converted before returning.
     * Falls back to the current session's accounting account when $account is null.
     */
    public static function getCredit(?Accounts $account = null): float
    {
        $account = self::resolve($account)->fresh();
        $credit  = (float) $account->credit;

        return self::toUsd($credit, $account);
    }

    /**
     * Increases the account's credit by the given USD amount.
     * The amount is converted from USD to the account's currency before storing.
     * Falls back to the current session's accounting account when $account is null.
     *
     * @return Accounts The refreshed accounting account.
     */
    public static function increase(?Accounts $account = null, float $amountUsd = 0): Accounts
    {
        $account                 = self::resolve($account);
        $amountInAccountCurrency = self::fromUsd($amountUsd, $account);

        $account->updateQuietly([
            'credit' => $account->credit + $amountInAccountCurrency,
        ]);

        return $account->fresh();
    }

    /**
     * Decreases the account's credit by the given USD amount.
     * The amount is converted from USD to the account's currency before storing.
     * Falls back to the current session's accounting account when $account is null.
     *
     * @return Accounts The refreshed accounting account.
     */
    public static function decrease(?Accounts $account = null, float $amountUsd = 0): Accounts
    {
        $account                 = self::resolve($account);
        $amountInAccountCurrency = self::fromUsd($amountUsd, $account);

        $account->updateQuietly([
            'credit' => $account->credit - $amountInAccountCurrency,
        ]);

        return $account->fresh();
    }

    /**
     * Returns whether the account has at least the given amount of credit (given in USD).
     * Falls back to the current session's accounting account when $account is null.
     */
    public static function hasEnoughCredit(?Accounts $account = null, float $amountUsd = 0): bool
    {
        return self::getCredit($account) >= $amountUsd;
    }

    /**
     * Throws InsufficientCreditException if the account does not have enough credit.
     * Use this as a guard at the start of any billable operation.
     * Falls back to the current session's accounting account when $account is null.
     *
     * @throws InsufficientCreditException
     */
    public static function check(?Accounts $account = null, float $amountUsd = 0, string $reason = ''): void
    {
        $available = self::getCredit($account);

        if ($available < $amountUsd) {
            throw new InsufficientCreditException($reason, $amountUsd, $available);
        }
    }

    /**
     * Resolves the accounting account, falling back to the current session's account when null.
     */
    private static function resolve(?Accounts $account): Accounts
    {
        return $account ?? AccountingHelper::getAccount();
    }

    /**
     * Resolves the currency code for the given accounting account.
     * Returns null if no currency is set.
     */
    public static function getCurrencyCode(Accounts $account): ?string
    {
        if (!$account->common_currency_id) {
            return null;
        }

        $currency = Currencies::where('id', $account->common_currency_id)->first();

        return $currency ? strtoupper($currency->code) : null;
    }

    /**
     * Converts a value stored in the account's currency to USD.
     * Returns the value unchanged if the account has no currency set or it is already USD.
     */
    private static function toUsd(float $amount, Accounts $account): float
    {
        $code = self::getCurrencyCode($account);

        if (!$code || $code === 'USD') {
            return $amount;
        }

        return ExchangeRateHelper::convert($code, 'USD', $amount);
    }

    /**
     * Converts a USD amount to the account's stored currency.
     * Returns the value unchanged if the account has no currency set or it is already USD.
     */
    private static function fromUsd(float $amountUsd, Accounts $account): float
    {
        $code = self::getCurrencyCode($account);

        if (!$code || $code === 'USD') {
            return $amountUsd;
        }

        return ExchangeRateHelper::convert('USD', $code, $amountUsd);
    }
}


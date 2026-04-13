<?php

namespace NextDeveloper\Accounting\Helpers;

use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Commons\Database\Models\Currencies;
use NextDeveloper\Commons\Helpers\ExchangeRateHelper;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

class CreditHelper
{
    /**
     * Returns the credit balance of the given accounting account in USD.
     * If the account's currency is not USD, the stored value is converted before returning.
     */
    public static function getCredit(Accounts $account): float
    {
        $credit = (float) $account->credit;

        return self::toUsd($credit, $account);
    }

    /**
     * Increases the account's credit by the given USD amount.
     * The amount is converted from USD to the account's currency before storing.
     *
     * @return Accounts The refreshed accounting account.
     */
    public static function increase(Accounts $account, float $amountUsd = 0): Accounts
    {
        $amountInAccountCurrency = self::fromUsd($amountUsd, $account);

        $account->updateQuietly([
            'credit' => $account->credit + $amountInAccountCurrency,
        ]);

        return $account->fresh();
    }

    /**
     * Decreases the account's credit by the given USD amount.
     * The amount is converted from USD to the account's currency before storing.
     *
     * @return Accounts The refreshed accounting account.
     */
    public static function decrease(Accounts $account, float $amountUsd = 0): Accounts
    {
        $amountInAccountCurrency = self::fromUsd($amountUsd, $account);

        $account->updateQuietly([
            'credit' => $account->credit - $amountInAccountCurrency,
        ]);

        return $account->fresh();
    }

    /**
     * Returns whether the account has at least the given amount of credit (given in USD).
     */
    public static function hasEnoughCredit(Accounts $account, float $amountUsd = 0): bool
    {
        return self::getCredit($account) >= $amountUsd;
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


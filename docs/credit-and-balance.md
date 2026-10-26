# Credit and balance

An accounting account (`accounting_accounts`) has two money columns. They mean different things and
are changed by different code. All amounts are stored in the account's own currency and
`CreditHelper` converts to and from USD.

| | `balance` | `credit` |
|---|---|---|
| What it is | What the account has paid in minus what it has used | A line of credit we grant to the account |
| Goes up | Payments, top-ups, refunds | Admin recharge, signup bonus |
| Goes down | Usage and charges | Only by an admin taking the grant back |
| Can be negative | Yes, once the account spends its granted credit | No |
| Changed by | `CreditHelper::increase / decrease / debit` | Set by hand, never by `CreditHelper` |

## How much can an account spend

```
available = balance + credit
```

Balance 0 and credit 100: the account can use up to $100 of resources. After $30 of usage the balance is
-30, the credit is still 100, and $70 is available. A payment of $30 brings the balance back to 0.

## CreditHelper

| Method | Returns |
|---|---|
| `getBalance()` | `balance` in USD |
| `getCredit()` | the granted `credit` in USD |
| `getAvailable()` | `balance + credit` in USD |
| `increase()` / `decrease()` / `debit()` | change `balance` only, and log a row in `accounting_credit_transactions` |
| `hasEnoughCredit()` / `check()` | compare against `getAvailable()` |

`accounting_credit_transactions.balance_after` is the `balance` after the change, never the credit.

## What to use where

- Show the customer what they paid or owe: `balance`. A negative balance is the amount to pay back.
- Block usage or a purchase: `getAvailable()` (or `check()`).
- Trigger an automatic top-up: `getBalance()`.
- Give a customer free room to spend: raise `credit`. Do not touch `balance`.

## Writing the columns yourself

Prefer `CreditHelper`: it locks the row, lets the database compute the new value, and writes the ledger.
If you must update the table directly, update only the column you mean, and note that raw updates skip model
events, so cached API payloads are not invalidated.

Both columns must have the same precision. Usage charges are fractions of a cent, and a column with two
decimals rounds them away.

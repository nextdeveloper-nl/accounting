<?php

return [
    'scopes'    =>  [
        'global' => [
            //  Dont do this here because it makes infinite loop with user object.
            '\NextDeveloper\IAM\Database\Scopes\AuthorizationScope',
            '\NextDeveloper\Commons\Database\GlobalScopes\LimitScope',
        ],
        // Staff sales managers see financial data of the accounts they manage only. Every table that carries
        // accounting_account_id and holds invoice/financial data must be listed here (sibling-path parity).
        'accounting_invoices_perspective' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_invoices' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_invoice_items' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_transactions' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_credit_transactions' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_usage_events' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_contracts' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_contracts_perspective' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_contract_items' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
        'accounting_contract_items_perspective' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
    ],
];

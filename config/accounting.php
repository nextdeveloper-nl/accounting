<?php

return [
    'scopes'    =>  [
        'global' => [
            //  Dont do this here because it makes infinite loop with user object.
            '\NextDeveloper\IAM\Database\Scopes\AuthorizationScope',
            '\NextDeveloper\Commons\Database\GlobalScopes\LimitScope',
        ],
        // Staff sales managers see invoices of the accounts they manage only.
        'accounting_invoices_perspective' => [
            '\NextDeveloper\Accounting\Database\Scopes\ManagedAccountsScope',
        ],
    ],
];

<?php

return [
    'scopes'    =>  [
        'global' => [
            //  Dont do this here because it makes infinite loop with user object.
            '\NextDeveloper\IAM\Database\Scopes\AuthorizationScope',
            '\NextDeveloper\Commons\Database\GlobalScopes\LimitScope',
        ]
    ],

    'actions'   =>  [
        \NextDeveloper\Accounting\Database\Models\Invoices::class   =>  [
            'pay'   =>  [
                'name'  =>  'Pay',
                'description'   =>  'This action tries to charge the customer for the payment, and if it is successful, it will mark the invoice as paid.'
            ]
        ]
    ]
];

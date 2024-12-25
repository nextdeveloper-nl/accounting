<?php

Route::prefix('accounting')->group(
    function () {
        Route::prefix('accounts')->group(
            function () {
                Route::get('/', 'Accounts\AccountsController@index');
                Route::get('/actions', 'Accounts\AccountsController@getActions');

                Route::get('{accounting_accounts}/tags ', 'Accounts\AccountsController@tags');
                Route::post('{accounting_accounts}/tags ', 'Accounts\AccountsController@saveTags');
                Route::get('{accounting_accounts}/addresses ', 'Accounts\AccountsController@addresses');
                Route::post('{accounting_accounts}/addresses ', 'Accounts\AccountsController@saveAddresses');

                Route::get('/{accounting_accounts}/{subObjects}', 'Accounts\AccountsController@relatedObjects');
                Route::get('/{accounting_accounts}', 'Accounts\AccountsController@show');

                Route::post('/', 'Accounts\AccountsController@store');
                Route::post('/{accounting_accounts}/do/{action}', 'Accounts\AccountsController@doAction');

                Route::patch('/{accounting_accounts}', 'Accounts\AccountsController@update');
                Route::delete('/{accounting_accounts}', 'Accounts\AccountsController@destroy');
            }
        );

        Route::prefix('invoices')->group(
            function () {
                Route::get('/', 'Invoices\InvoicesController@index');
                Route::get('/actions', 'Invoices\InvoicesController@getActions');

                Route::get('{accounting_invoices}/tags ', 'Invoices\InvoicesController@tags');
                Route::post('{accounting_invoices}/tags ', 'Invoices\InvoicesController@saveTags');
                Route::get('{accounting_invoices}/addresses ', 'Invoices\InvoicesController@addresses');
                Route::post('{accounting_invoices}/addresses ', 'Invoices\InvoicesController@saveAddresses');

                Route::get('/{accounting_invoices}/{subObjects}', 'Invoices\InvoicesController@relatedObjects');
                Route::get('/{accounting_invoices}', 'Invoices\InvoicesController@show');

                Route::post('/', 'Invoices\InvoicesController@store');
                Route::post('/{accounting_invoices}/do/{action}', 'Invoices\InvoicesController@doAction');

                Route::patch('/{accounting_invoices}', 'Invoices\InvoicesController@update');
                Route::delete('/{accounting_invoices}', 'Invoices\InvoicesController@destroy');
            }
        );

        Route::prefix('payment-gateway-messages')->group(
            function () {
                Route::get('/', 'PaymentGatewayMessages\PaymentGatewayMessagesController@index');
                Route::get('/actions', 'PaymentGatewayMessages\PaymentGatewayMessagesController@getActions');

                Route::get('{apgm}/tags ', 'PaymentGatewayMessages\PaymentGatewayMessagesController@tags');
                Route::post('{apgm}/tags ', 'PaymentGatewayMessages\PaymentGatewayMessagesController@saveTags');
                Route::get('{apgm}/addresses ', 'PaymentGatewayMessages\PaymentGatewayMessagesController@addresses');
                Route::post('{apgm}/addresses ', 'PaymentGatewayMessages\PaymentGatewayMessagesController@saveAddresses');

                Route::get('/{apgm}/{subObjects}', 'PaymentGatewayMessages\PaymentGatewayMessagesController@relatedObjects');
                Route::get('/{apgm}', 'PaymentGatewayMessages\PaymentGatewayMessagesController@show');

                Route::post('/', 'PaymentGatewayMessages\PaymentGatewayMessagesController@store');
                Route::post('/{apgm}/do/{action}', 'PaymentGatewayMessages\PaymentGatewayMessagesController@doAction');

                Route::patch('/{apgm}', 'PaymentGatewayMessages\PaymentGatewayMessagesController@update');
                Route::delete('/{apgm}', 'PaymentGatewayMessages\PaymentGatewayMessagesController@destroy');
            }
        );

        Route::prefix('invoice-items')->group(
            function () {
                Route::get('/', 'InvoiceItems\InvoiceItemsController@index');
                Route::get('/actions', 'InvoiceItems\InvoiceItemsController@getActions');

                Route::get('{accounting_invoice_items}/tags ', 'InvoiceItems\InvoiceItemsController@tags');
                Route::post('{accounting_invoice_items}/tags ', 'InvoiceItems\InvoiceItemsController@saveTags');
                Route::get('{accounting_invoice_items}/addresses ', 'InvoiceItems\InvoiceItemsController@addresses');
                Route::post('{accounting_invoice_items}/addresses ', 'InvoiceItems\InvoiceItemsController@saveAddresses');

                Route::get('/{accounting_invoice_items}/{subObjects}', 'InvoiceItems\InvoiceItemsController@relatedObjects');
                Route::get('/{accounting_invoice_items}', 'InvoiceItems\InvoiceItemsController@show');

                Route::post('/', 'InvoiceItems\InvoiceItemsController@store');
                Route::post('/{accounting_invoice_items}/do/{action}', 'InvoiceItems\InvoiceItemsController@doAction');

                Route::patch('/{accounting_invoice_items}', 'InvoiceItems\InvoiceItemsController@update');
                Route::delete('/{accounting_invoice_items}', 'InvoiceItems\InvoiceItemsController@destroy');
            }
        );

        Route::prefix('transactions')->group(
            function () {
                Route::get('/', 'Transactions\TransactionsController@index');
                Route::get('/actions', 'Transactions\TransactionsController@getActions');

                Route::get('{accounting_transactions}/tags ', 'Transactions\TransactionsController@tags');
                Route::post('{accounting_transactions}/tags ', 'Transactions\TransactionsController@saveTags');
                Route::get('{accounting_transactions}/addresses ', 'Transactions\TransactionsController@addresses');
                Route::post('{accounting_transactions}/addresses ', 'Transactions\TransactionsController@saveAddresses');

                Route::get('/{accounting_transactions}/{subObjects}', 'Transactions\TransactionsController@relatedObjects');
                Route::get('/{accounting_transactions}', 'Transactions\TransactionsController@show');

                Route::post('/', 'Transactions\TransactionsController@store');
                Route::post('/{accounting_transactions}/do/{action}', 'Transactions\TransactionsController@doAction');

                Route::patch('/{accounting_transactions}', 'Transactions\TransactionsController@update');
                Route::delete('/{accounting_transactions}', 'Transactions\TransactionsController@destroy');
            }
        );

        Route::prefix('payment-gateways')->group(
            function () {
                Route::get('/', 'PaymentGateways\PaymentGatewaysController@index');
                Route::get('/actions', 'PaymentGateways\PaymentGatewaysController@getActions');

                Route::get('{accounting_payment_gateways}/tags ', 'PaymentGateways\PaymentGatewaysController@tags');
                Route::post('{accounting_payment_gateways}/tags ', 'PaymentGateways\PaymentGatewaysController@saveTags');
                Route::get('{accounting_payment_gateways}/addresses ', 'PaymentGateways\PaymentGatewaysController@addresses');
                Route::post('{accounting_payment_gateways}/addresses ', 'PaymentGateways\PaymentGatewaysController@saveAddresses');

                Route::get('/{accounting_payment_gateways}/{subObjects}', 'PaymentGateways\PaymentGatewaysController@relatedObjects');
                Route::get('/{accounting_payment_gateways}', 'PaymentGateways\PaymentGatewaysController@show');

                Route::post('/', 'PaymentGateways\PaymentGatewaysController@store');
                Route::post('/{accounting_payment_gateways}/do/{action}', 'PaymentGateways\PaymentGatewaysController@doAction');

                Route::patch('/{accounting_payment_gateways}', 'PaymentGateways\PaymentGatewaysController@update');
                Route::delete('/{accounting_payment_gateways}', 'PaymentGateways\PaymentGatewaysController@destroy');
            }
        );

        Route::prefix('credit-cards')->group(
            function () {
                Route::get('/', 'CreditCards\CreditCardsController@index');
                Route::get('/actions', 'CreditCards\CreditCardsController@getActions');

                Route::get('{accounting_credit_cards}/tags ', 'CreditCards\CreditCardsController@tags');
                Route::post('{accounting_credit_cards}/tags ', 'CreditCards\CreditCardsController@saveTags');
                Route::get('{accounting_credit_cards}/addresses ', 'CreditCards\CreditCardsController@addresses');
                Route::post('{accounting_credit_cards}/addresses ', 'CreditCards\CreditCardsController@saveAddresses');

                Route::get('/{accounting_credit_cards}/{subObjects}', 'CreditCards\CreditCardsController@relatedObjects');
                Route::get('/{accounting_credit_cards}', 'CreditCards\CreditCardsController@show');

                Route::post('/', 'CreditCards\CreditCardsController@store');
                Route::post('/{accounting_credit_cards}/do/{action}', 'CreditCards\CreditCardsController@doAction');

                Route::patch('/{accounting_credit_cards}', 'CreditCards\CreditCardsController@update');
                Route::delete('/{accounting_credit_cards}', 'CreditCards\CreditCardsController@destroy');
            }
        );

        Route::prefix('promo-codes')->group(
            function () {
                Route::get('/', 'PromoCodes\PromoCodesController@index');
                Route::get('/actions', 'PromoCodes\PromoCodesController@getActions');

                Route::get('{accounting_promo_codes}/tags ', 'PromoCodes\PromoCodesController@tags');
                Route::post('{accounting_promo_codes}/tags ', 'PromoCodes\PromoCodesController@saveTags');
                Route::get('{accounting_promo_codes}/addresses ', 'PromoCodes\PromoCodesController@addresses');
                Route::post('{accounting_promo_codes}/addresses ', 'PromoCodes\PromoCodesController@saveAddresses');

                Route::get('/{accounting_promo_codes}/{subObjects}', 'PromoCodes\PromoCodesController@relatedObjects');
                Route::get('/{accounting_promo_codes}', 'PromoCodes\PromoCodesController@show');

                Route::post('/', 'PromoCodes\PromoCodesController@store');
                Route::post('/{accounting_promo_codes}/do/{action}', 'PromoCodes\PromoCodesController@doAction');

                Route::patch('/{accounting_promo_codes}', 'PromoCodes\PromoCodesController@update');
                Route::delete('/{accounting_promo_codes}', 'PromoCodes\PromoCodesController@destroy');
            }
        );

        Route::prefix('accounts-perspective')->group(
            function () {
                Route::get('/', 'AccountsPerspective\AccountsPerspectiveController@index');
                Route::get('/actions', 'AccountsPerspective\AccountsPerspectiveController@getActions');

                Route::get('{accounting_accounts_perspective}/tags ', 'AccountsPerspective\AccountsPerspectiveController@tags');
                Route::post('{accounting_accounts_perspective}/tags ', 'AccountsPerspective\AccountsPerspectiveController@saveTags');
                Route::get('{accounting_accounts_perspective}/addresses ', 'AccountsPerspective\AccountsPerspectiveController@addresses');
                Route::post('{accounting_accounts_perspective}/addresses ', 'AccountsPerspective\AccountsPerspectiveController@saveAddresses');

                Route::get('/{accounting_accounts_perspective}/{subObjects}', 'AccountsPerspective\AccountsPerspectiveController@relatedObjects');
                Route::get('/{accounting_accounts_perspective}', 'AccountsPerspective\AccountsPerspectiveController@show');

                Route::post('/', 'AccountsPerspective\AccountsPerspectiveController@store');
                Route::post('/{accounting_accounts_perspective}/do/{action}', 'AccountsPerspective\AccountsPerspectiveController@doAction');

                Route::patch('/{accounting_accounts_perspective}', 'AccountsPerspective\AccountsPerspectiveController@update');
                Route::delete('/{accounting_accounts_perspective}', 'AccountsPerspective\AccountsPerspectiveController@destroy');
            }
        );

        Route::prefix('invoices-perspective')->group(
            function () {
                Route::get('/', 'InvoicesPerspective\InvoicesPerspectiveController@index');
                Route::get('/actions', 'InvoicesPerspective\InvoicesPerspectiveController@getActions');

                Route::get('{accounting_invoices_perspective}/tags ', 'InvoicesPerspective\InvoicesPerspectiveController@tags');
                Route::post('{accounting_invoices_perspective}/tags ', 'InvoicesPerspective\InvoicesPerspectiveController@saveTags');
                Route::get('{accounting_invoices_perspective}/addresses ', 'InvoicesPerspective\InvoicesPerspectiveController@addresses');
                Route::post('{accounting_invoices_perspective}/addresses ', 'InvoicesPerspective\InvoicesPerspectiveController@saveAddresses');

                Route::get('/{accounting_invoices_perspective}/{subObjects}', 'InvoicesPerspective\InvoicesPerspectiveController@relatedObjects');
                Route::get('/{accounting_invoices_perspective}', 'InvoicesPerspective\InvoicesPerspectiveController@show');

                Route::post('/', 'InvoicesPerspective\InvoicesPerspectiveController@store');
                Route::post('/{accounting_invoices_perspective}/do/{action}', 'InvoicesPerspective\InvoicesPerspectiveController@doAction');

                Route::patch('/{accounting_invoices_perspective}', 'InvoicesPerspective\InvoicesPerspectiveController@update');
                Route::delete('/{accounting_invoices_perspective}', 'InvoicesPerspective\InvoicesPerspectiveController@destroy');
            }
        );

        // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE
























































































































































    }
);




























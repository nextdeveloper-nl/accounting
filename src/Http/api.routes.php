<?php

Route::prefix('accounting')->group(
    function () {
        Route::prefix('accounts')->group(
            function () {
                Route::get('/', 'Accounts\AccountsController@index');

                Route::get('{accounting_accounts}/tags ', 'Accounts\AccountsController@tags');
                Route::post('{accounting_accounts}/tags ', 'Accounts\AccountsController@saveTags');
                Route::get('{accounting_accounts}/addresses ', 'Accounts\AccountsController@addresses');
                Route::post('{accounting_accounts}/addresses ', 'Accounts\AccountsController@saveAddresses');

                Route::get('/{accounting_accounts}/{subObjects}', 'Accounts\AccountsController@relatedObjects');
                Route::get('/{accounting_accounts}', 'Accounts\AccountsController@show');

                Route::post('/', 'Accounts\AccountsController@store');
                Route::patch('/{accounting_accounts}', 'Accounts\AccountsController@update');
                Route::delete('/{accounting_accounts}', 'Accounts\AccountsController@destroy');
            }
        );

        Route::prefix('invoices')->group(
            function () {
                Route::get('/', 'Invoices\InvoicesController@index');

                Route::get('{accounting_invoices}/tags ', 'Invoices\InvoicesController@tags');
                Route::post('{accounting_invoices}/tags ', 'Invoices\InvoicesController@saveTags');
                Route::get('{accounting_invoices}/addresses ', 'Invoices\InvoicesController@addresses');
                Route::post('{accounting_invoices}/addresses ', 'Invoices\InvoicesController@saveAddresses');

                Route::get('/{accounting_invoices}/{subObjects}', 'Invoices\InvoicesController@relatedObjects');
                Route::get('/{accounting_invoices}', 'Invoices\InvoicesController@show');

                Route::post('/', 'Invoices\InvoicesController@store');
                Route::patch('/{accounting_invoices}', 'Invoices\InvoicesController@update');
                Route::delete('/{accounting_invoices}', 'Invoices\InvoicesController@destroy');
            }
        );

        Route::prefix('invoice-items')->group(
            function () {
                Route::get('/', 'InvoiceItems\InvoiceItemsController@index');

                Route::get('{accounting_invoice_items}/tags ', 'InvoiceItems\InvoiceItemsController@tags');
                Route::post('{accounting_invoice_items}/tags ', 'InvoiceItems\InvoiceItemsController@saveTags');
                Route::get('{accounting_invoice_items}/addresses ', 'InvoiceItems\InvoiceItemsController@addresses');
                Route::post('{accounting_invoice_items}/addresses ', 'InvoiceItems\InvoiceItemsController@saveAddresses');

                Route::get('/{accounting_invoice_items}/{subObjects}', 'InvoiceItems\InvoiceItemsController@relatedObjects');
                Route::get('/{accounting_invoice_items}', 'InvoiceItems\InvoiceItemsController@show');

                Route::post('/', 'InvoiceItems\InvoiceItemsController@store');
                Route::patch('/{accounting_invoice_items}', 'InvoiceItems\InvoiceItemsController@update');
                Route::delete('/{accounting_invoice_items}', 'InvoiceItems\InvoiceItemsController@destroy');
            }
        );

        Route::prefix('payment-gateways')->group(
            function () {
                Route::get('/', 'PaymentGateways\PaymentGatewaysController@index');

                Route::get('{accounting_payment_gateways}/tags ', 'PaymentGateways\PaymentGatewaysController@tags');
                Route::post('{accounting_payment_gateways}/tags ', 'PaymentGateways\PaymentGatewaysController@saveTags');
                Route::get('{accounting_payment_gateways}/addresses ', 'PaymentGateways\PaymentGatewaysController@addresses');
                Route::post('{accounting_payment_gateways}/addresses ', 'PaymentGateways\PaymentGatewaysController@saveAddresses');

                Route::get('/{accounting_payment_gateways}/{subObjects}', 'PaymentGateways\PaymentGatewaysController@relatedObjects');
                Route::get('/{accounting_payment_gateways}', 'PaymentGateways\PaymentGatewaysController@show');

                Route::post('/', 'PaymentGateways\PaymentGatewaysController@store');
                Route::patch('/{accounting_payment_gateways}', 'PaymentGateways\PaymentGatewaysController@update');
                Route::delete('/{accounting_payment_gateways}', 'PaymentGateways\PaymentGatewaysController@destroy');
            }
        );

        Route::prefix('transactions')->group(
            function () {
                Route::get('/', 'Transactions\TransactionsController@index');

                Route::get('{accounting_transactions}/tags ', 'Transactions\TransactionsController@tags');
                Route::post('{accounting_transactions}/tags ', 'Transactions\TransactionsController@saveTags');
                Route::get('{accounting_transactions}/addresses ', 'Transactions\TransactionsController@addresses');
                Route::post('{accounting_transactions}/addresses ', 'Transactions\TransactionsController@saveAddresses');

                Route::get('/{accounting_transactions}/{subObjects}', 'Transactions\TransactionsController@relatedObjects');
                Route::get('/{accounting_transactions}', 'Transactions\TransactionsController@show');

                Route::post('/', 'Transactions\TransactionsController@store');
                Route::patch('/{accounting_transactions}', 'Transactions\TransactionsController@update');
                Route::delete('/{accounting_transactions}', 'Transactions\TransactionsController@destroy');
            }
        );

        // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE





































    }
);









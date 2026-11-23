<?php

Route::prefix('accounting')->group(function() {

// MULTI-TENANT PARTNERSHIP GET ROUTES
//  These fixed paths MUST be registered before the generated '/{accounting_partnerships}' and
//  '/{accounting_partner_programs}' routes, otherwise 'me' and 'invite-candidates' are taken as an id.
Route::get('partner-programs/me', 'PartnerPrograms\\PartnerProgramsController@me');
//  Programs of other companies that take applications (before the generated '/{accounting_partner_programs}' route).
Route::get('partner-programs/discover', 'PartnerPrograms\\PartnerProgramsController@discover');
Route::get('partnerships/invite-candidates', 'Partnerships\\PartnershipsController@inviteCandidates');
Route::get('account-partners/customer-candidates', 'AccountPartners\\AccountPartnersController@customerCandidates');

Route::prefix('accounts')->group(function () {
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
    });

Route::prefix('partnerships')->group(function () {
        Route::get('/', 'Partnerships\PartnershipsController@index');
        Route::get('/actions', 'Partnerships\PartnershipsController@getActions');

Route::get('{accounting_partnerships}/tags ', 'Partnerships\PartnershipsController@tags');
Route::post('{accounting_partnerships}/tags ', 'Partnerships\PartnershipsController@saveTags');
Route::get('{accounting_partnerships}/addresses ', 'Partnerships\PartnershipsController@addresses');
Route::post('{accounting_partnerships}/addresses ', 'Partnerships\PartnershipsController@saveAddresses');

        Route::get('/{accounting_partnerships}/{subObjects}', 'Partnerships\PartnershipsController@relatedObjects');
        Route::get('/{accounting_partnerships}', 'Partnerships\PartnershipsController@show');

        Route::post('/', 'Partnerships\PartnershipsController@store');
        Route::post('/{accounting_partnerships}/do/{action}', 'Partnerships\PartnershipsController@doAction');

        Route::patch('/{accounting_partnerships}', 'Partnerships\PartnershipsController@update');
        Route::delete('/{accounting_partnerships}', 'Partnerships\PartnershipsController@destroy');
    });

Route::prefix('contract-items')->group(function () {
        Route::get('/', 'ContractItems\ContractItemsController@index');
        Route::get('/actions', 'ContractItems\ContractItemsController@getActions');

Route::get('{accounting_contract_items}/tags ', 'ContractItems\ContractItemsController@tags');
Route::post('{accounting_contract_items}/tags ', 'ContractItems\ContractItemsController@saveTags');
Route::get('{accounting_contract_items}/addresses ', 'ContractItems\ContractItemsController@addresses');
Route::post('{accounting_contract_items}/addresses ', 'ContractItems\ContractItemsController@saveAddresses');

        Route::get('/{accounting_contract_items}/{subObjects}', 'ContractItems\ContractItemsController@relatedObjects');
        Route::get('/{accounting_contract_items}', 'ContractItems\ContractItemsController@show');

        Route::post('/', 'ContractItems\ContractItemsController@store');
        Route::post('/{accounting_contract_items}/do/{action}', 'ContractItems\ContractItemsController@doAction');

        Route::patch('/{accounting_contract_items}', 'ContractItems\ContractItemsController@update');
        Route::delete('/{accounting_contract_items}', 'ContractItems\ContractItemsController@destroy');
    });

Route::prefix('contracts')->group(function () {
        Route::get('/', 'Contracts\ContractsController@index');
        Route::get('/actions', 'Contracts\ContractsController@getActions');

Route::get('{accounting_contracts}/tags ', 'Contracts\ContractsController@tags');
Route::post('{accounting_contracts}/tags ', 'Contracts\ContractsController@saveTags');
Route::get('{accounting_contracts}/addresses ', 'Contracts\ContractsController@addresses');
Route::post('{accounting_contracts}/addresses ', 'Contracts\ContractsController@saveAddresses');

        Route::get('/{accounting_contracts}/{subObjects}', 'Contracts\ContractsController@relatedObjects');
        Route::get('/{accounting_contracts}', 'Contracts\ContractsController@show');

        Route::post('/', 'Contracts\ContractsController@store');
        Route::post('/{accounting_contracts}/do/{action}', 'Contracts\ContractsController@doAction');

        Route::patch('/{accounting_contracts}', 'Contracts\ContractsController@update');
        Route::delete('/{accounting_contracts}', 'Contracts\ContractsController@destroy');
    });

Route::prefix('invoice-items')->group(function () {
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
    });

Route::prefix('invoices')->group(function () {
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
    });

Route::prefix('partner-assignments')->group(function () {
        Route::get('/', 'PartnerAssignments\PartnerAssignmentsController@index');
        Route::get('/actions', 'PartnerAssignments\PartnerAssignmentsController@getActions');

Route::get('{accounting_partner_assignments}/tags ', 'PartnerAssignments\PartnerAssignmentsController@tags');
Route::post('{accounting_partner_assignments}/tags ', 'PartnerAssignments\PartnerAssignmentsController@saveTags');
Route::get('{accounting_partner_assignments}/addresses ', 'PartnerAssignments\PartnerAssignmentsController@addresses');
Route::post('{accounting_partner_assignments}/addresses ', 'PartnerAssignments\PartnerAssignmentsController@saveAddresses');

        Route::get('/{accounting_partner_assignments}/{subObjects}', 'PartnerAssignments\PartnerAssignmentsController@relatedObjects');
        Route::get('/{accounting_partner_assignments}', 'PartnerAssignments\PartnerAssignmentsController@show');

        Route::post('/', 'PartnerAssignments\PartnerAssignmentsController@store');
        Route::post('/{accounting_partner_assignments}/do/{action}', 'PartnerAssignments\PartnerAssignmentsController@doAction');

        Route::patch('/{accounting_partner_assignments}', 'PartnerAssignments\PartnerAssignmentsController@update');
        Route::delete('/{accounting_partner_assignments}', 'PartnerAssignments\PartnerAssignmentsController@destroy');
    });

Route::prefix('payment-checkout-sessions')->group(function () {
        Route::get('/', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@index');
        Route::get('/actions', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@getActions');

Route::get('{apcs}/tags ', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@tags');
Route::post('{apcs}/tags ', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@saveTags');
Route::get('{apcs}/addresses ', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@addresses');
Route::post('{apcs}/addresses ', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@saveAddresses');

        Route::get('/{apcs}/{subObjects}', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@relatedObjects');
        Route::get('/{apcs}', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@show');

        Route::post('/', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@store');
        Route::post('/{apcs}/do/{action}', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@doAction');

        Route::patch('/{apcs}', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@update');
        Route::delete('/{apcs}', 'PaymentCheckoutSessions\PaymentCheckoutSessionsController@destroy');
    });

Route::prefix('payment-gateway-messages')->group(function () {
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
    });

Route::prefix('payment-gateways')->group(function () {
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
    });

Route::prefix('promo-codes')->group(function () {
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
    });

Route::prefix('transactions')->group(function () {
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
    });

Route::prefix('credit-cards')->group(function () {
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
    });

Route::prefix('credit-transactions')->group(function () {
        Route::get('/', 'CreditTransactions\CreditTransactionsController@index');
        Route::get('/actions', 'CreditTransactions\CreditTransactionsController@getActions');

Route::get('{accounting_credit_transactions}/tags ', 'CreditTransactions\CreditTransactionsController@tags');
Route::post('{accounting_credit_transactions}/tags ', 'CreditTransactions\CreditTransactionsController@saveTags');
Route::get('{accounting_credit_transactions}/addresses ', 'CreditTransactions\CreditTransactionsController@addresses');
Route::post('{accounting_credit_transactions}/addresses ', 'CreditTransactions\CreditTransactionsController@saveAddresses');

        Route::get('/{accounting_credit_transactions}/{subObjects}', 'CreditTransactions\CreditTransactionsController@relatedObjects');
        Route::get('/{accounting_credit_transactions}', 'CreditTransactions\CreditTransactionsController@show');

        Route::post('/', 'CreditTransactions\CreditTransactionsController@store');
        Route::post('/{accounting_credit_transactions}/do/{action}', 'CreditTransactions\CreditTransactionsController@doAction');

        Route::patch('/{accounting_credit_transactions}', 'CreditTransactions\CreditTransactionsController@update');
        Route::delete('/{accounting_credit_transactions}', 'CreditTransactions\CreditTransactionsController@destroy');
    });

Route::prefix('accounts-perspective')->group(function () {
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
    });

Route::prefix('affiliates-perspective')->group(function () {
        Route::get('/', 'AffiliatesPerspective\AffiliatesPerspectiveController@index');
        Route::get('/actions', 'AffiliatesPerspective\AffiliatesPerspectiveController@getActions');

Route::get('{aap}/tags ', 'AffiliatesPerspective\AffiliatesPerspectiveController@tags');
Route::post('{aap}/tags ', 'AffiliatesPerspective\AffiliatesPerspectiveController@saveTags');
Route::get('{aap}/addresses ', 'AffiliatesPerspective\AffiliatesPerspectiveController@addresses');
Route::post('{aap}/addresses ', 'AffiliatesPerspective\AffiliatesPerspectiveController@saveAddresses');

        Route::get('/{aap}/{subObjects}', 'AffiliatesPerspective\AffiliatesPerspectiveController@relatedObjects');
        Route::get('/{aap}', 'AffiliatesPerspective\AffiliatesPerspectiveController@show');

        Route::post('/', 'AffiliatesPerspective\AffiliatesPerspectiveController@store');
        Route::post('/{aap}/do/{action}', 'AffiliatesPerspective\AffiliatesPerspectiveController@doAction');

        Route::patch('/{aap}', 'AffiliatesPerspective\AffiliatesPerspectiveController@update');
        Route::delete('/{aap}', 'AffiliatesPerspective\AffiliatesPerspectiveController@destroy');
    });

Route::prefix('contract-items-perspective')->group(function () {
        Route::get('/', 'ContractItemsPerspective\ContractItemsPerspectiveController@index');
        Route::get('/actions', 'ContractItemsPerspective\ContractItemsPerspectiveController@getActions');

Route::get('{acip}/tags ', 'ContractItemsPerspective\ContractItemsPerspectiveController@tags');
Route::post('{acip}/tags ', 'ContractItemsPerspective\ContractItemsPerspectiveController@saveTags');
Route::get('{acip}/addresses ', 'ContractItemsPerspective\ContractItemsPerspectiveController@addresses');
Route::post('{acip}/addresses ', 'ContractItemsPerspective\ContractItemsPerspectiveController@saveAddresses');

        Route::get('/{acip}/{subObjects}', 'ContractItemsPerspective\ContractItemsPerspectiveController@relatedObjects');
        Route::get('/{acip}', 'ContractItemsPerspective\ContractItemsPerspectiveController@show');

        Route::post('/', 'ContractItemsPerspective\ContractItemsPerspectiveController@store');
        Route::post('/{acip}/do/{action}', 'ContractItemsPerspective\ContractItemsPerspectiveController@doAction');

        Route::patch('/{acip}', 'ContractItemsPerspective\ContractItemsPerspectiveController@update');
        Route::delete('/{acip}', 'ContractItemsPerspective\ContractItemsPerspectiveController@destroy');
    });

Route::prefix('contracts-perspective')->group(function () {
        Route::get('/', 'ContractsPerspective\ContractsPerspectiveController@index');
        Route::get('/actions', 'ContractsPerspective\ContractsPerspectiveController@getActions');

Route::get('{accounting_contracts_perspective}/tags ', 'ContractsPerspective\ContractsPerspectiveController@tags');
Route::post('{accounting_contracts_perspective}/tags ', 'ContractsPerspective\ContractsPerspectiveController@saveTags');
Route::get('{accounting_contracts_perspective}/addresses ', 'ContractsPerspective\ContractsPerspectiveController@addresses');
Route::post('{accounting_contracts_perspective}/addresses ', 'ContractsPerspective\ContractsPerspectiveController@saveAddresses');

        Route::get('/{accounting_contracts_perspective}/{subObjects}', 'ContractsPerspective\ContractsPerspectiveController@relatedObjects');
        Route::get('/{accounting_contracts_perspective}', 'ContractsPerspective\ContractsPerspectiveController@show');

        Route::post('/', 'ContractsPerspective\ContractsPerspectiveController@store');
        Route::post('/{accounting_contracts_perspective}/do/{action}', 'ContractsPerspective\ContractsPerspectiveController@doAction');

        Route::patch('/{accounting_contracts_perspective}', 'ContractsPerspective\ContractsPerspectiveController@update');
        Route::delete('/{accounting_contracts_perspective}', 'ContractsPerspective\ContractsPerspectiveController@destroy');
    });

Route::prefix('integrators-perspective')->group(function () {
        Route::get('/', 'IntegratorsPerspective\IntegratorsPerspectiveController@index');
        Route::get('/actions', 'IntegratorsPerspective\IntegratorsPerspectiveController@getActions');

Route::get('{aip}/tags ', 'IntegratorsPerspective\IntegratorsPerspectiveController@tags');
Route::post('{aip}/tags ', 'IntegratorsPerspective\IntegratorsPerspectiveController@saveTags');
Route::get('{aip}/addresses ', 'IntegratorsPerspective\IntegratorsPerspectiveController@addresses');
Route::post('{aip}/addresses ', 'IntegratorsPerspective\IntegratorsPerspectiveController@saveAddresses');

        Route::get('/{aip}/{subObjects}', 'IntegratorsPerspective\IntegratorsPerspectiveController@relatedObjects');
        Route::get('/{aip}', 'IntegratorsPerspective\IntegratorsPerspectiveController@show');

        Route::post('/', 'IntegratorsPerspective\IntegratorsPerspectiveController@store');
        Route::post('/{aip}/do/{action}', 'IntegratorsPerspective\IntegratorsPerspectiveController@doAction');

        Route::patch('/{aip}', 'IntegratorsPerspective\IntegratorsPerspectiveController@update');
        Route::delete('/{aip}', 'IntegratorsPerspective\IntegratorsPerspectiveController@destroy');
    });

Route::prefix('distributors-perspective')->group(function () {
        Route::get('/', 'DistributorsPerspective\DistributorsPerspectiveController@index');
        Route::get('/actions', 'DistributorsPerspective\DistributorsPerspectiveController@getActions');

Route::get('{adp}/tags ', 'DistributorsPerspective\DistributorsPerspectiveController@tags');
Route::post('{adp}/tags ', 'DistributorsPerspective\DistributorsPerspectiveController@saveTags');
Route::get('{adp}/addresses ', 'DistributorsPerspective\DistributorsPerspectiveController@addresses');
Route::post('{adp}/addresses ', 'DistributorsPerspective\DistributorsPerspectiveController@saveAddresses');

        Route::get('/{adp}/{subObjects}', 'DistributorsPerspective\DistributorsPerspectiveController@relatedObjects');
        Route::get('/{adp}', 'DistributorsPerspective\DistributorsPerspectiveController@show');

        Route::post('/', 'DistributorsPerspective\DistributorsPerspectiveController@store');
        Route::post('/{adp}/do/{action}', 'DistributorsPerspective\DistributorsPerspectiveController@doAction');

        Route::patch('/{adp}', 'DistributorsPerspective\DistributorsPerspectiveController@update');
        Route::delete('/{adp}', 'DistributorsPerspective\DistributorsPerspectiveController@destroy');
    });

Route::prefix('invoice-items-perspective')->group(function () {
        Route::get('/', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@index');
        Route::get('/actions', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@getActions');

Route::get('{aiip}/tags ', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@tags');
Route::post('{aiip}/tags ', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@saveTags');
Route::get('{aiip}/addresses ', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@addresses');
Route::post('{aiip}/addresses ', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@saveAddresses');

        Route::get('/{aiip}/{subObjects}', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@relatedObjects');
        Route::get('/{aiip}', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@show');

        Route::post('/', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@store');
        Route::post('/{aiip}/do/{action}', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@doAction');

        Route::patch('/{aiip}', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@update');
        Route::delete('/{aiip}', 'InvoiceItemsPerspective\InvoiceItemsPerspectiveController@destroy');
    });

Route::prefix('invoices-perspective')->group(function () {
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
    });

Route::prefix('monthly-paid-invoices-performance')->group(function () {
        Route::get('/', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@index');
        Route::get('/actions', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@getActions');

Route::get('{ampip}/tags ', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@tags');
Route::post('{ampip}/tags ', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@saveTags');
Route::get('{ampip}/addresses ', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@addresses');
Route::post('{ampip}/addresses ', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@saveAddresses');

        Route::get('/{ampip}/{subObjects}', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@relatedObjects');
        Route::get('/{ampip}', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@show');

        Route::post('/', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@store');
        Route::post('/{ampip}/do/{action}', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@doAction');

        Route::patch('/{ampip}', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@update');
        Route::delete('/{ampip}', 'MonthlyPaidInvoicesPerformance\MonthlyPaidInvoicesPerformanceController@destroy');
    });

Route::prefix('partnerships-perspective')->group(function () {
        Route::get('/', 'PartnershipsPerspective\PartnershipsPerspectiveController@index');
        Route::get('/actions', 'PartnershipsPerspective\PartnershipsPerspectiveController@getActions');

Route::get('{app}/tags ', 'PartnershipsPerspective\PartnershipsPerspectiveController@tags');
Route::post('{app}/tags ', 'PartnershipsPerspective\PartnershipsPerspectiveController@saveTags');
Route::get('{app}/addresses ', 'PartnershipsPerspective\PartnershipsPerspectiveController@addresses');
Route::post('{app}/addresses ', 'PartnershipsPerspective\PartnershipsPerspectiveController@saveAddresses');

        Route::get('/{app}/{subObjects}', 'PartnershipsPerspective\PartnershipsPerspectiveController@relatedObjects');
        Route::get('/{app}', 'PartnershipsPerspective\PartnershipsPerspectiveController@show');

        Route::post('/', 'PartnershipsPerspective\PartnershipsPerspectiveController@store');
        Route::post('/{app}/do/{action}', 'PartnershipsPerspective\PartnershipsPerspectiveController@doAction');

        Route::patch('/{app}', 'PartnershipsPerspective\PartnershipsPerspectiveController@update');
        Route::delete('/{app}', 'PartnershipsPerspective\PartnershipsPerspectiveController@destroy');
    });

Route::prefix('sales-partners-perspective')->group(function () {
        Route::get('/', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@index');
        Route::get('/actions', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@getActions');

Route::get('{aspp}/tags ', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@tags');
Route::post('{aspp}/tags ', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@saveTags');
Route::get('{aspp}/addresses ', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@addresses');
Route::post('{aspp}/addresses ', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@saveAddresses');

        Route::get('/{aspp}/{subObjects}', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@relatedObjects');
        Route::get('/{aspp}', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@show');

        Route::post('/', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@store');
        Route::post('/{aspp}/do/{action}', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@doAction');

        Route::patch('/{aspp}', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@update');
        Route::delete('/{aspp}', 'SalesPartnersPerspective\SalesPartnersPerspectiveController@destroy');
    });

Route::prefix('vendors-perspective')->group(function () {
        Route::get('/', 'VendorsPerspective\VendorsPerspectiveController@index');
        Route::get('/actions', 'VendorsPerspective\VendorsPerspectiveController@getActions');

Route::get('{accounting_vendors_perspective}/tags ', 'VendorsPerspective\VendorsPerspectiveController@tags');
Route::post('{accounting_vendors_perspective}/tags ', 'VendorsPerspective\VendorsPerspectiveController@saveTags');
Route::get('{accounting_vendors_perspective}/addresses ', 'VendorsPerspective\VendorsPerspectiveController@addresses');
Route::post('{accounting_vendors_perspective}/addresses ', 'VendorsPerspective\VendorsPerspectiveController@saveAddresses');

        Route::get('/{accounting_vendors_perspective}/{subObjects}', 'VendorsPerspective\VendorsPerspectiveController@relatedObjects');
        Route::get('/{accounting_vendors_perspective}', 'VendorsPerspective\VendorsPerspectiveController@show');

        Route::post('/', 'VendorsPerspective\VendorsPerspectiveController@store');
        Route::post('/{accounting_vendors_perspective}/do/{action}', 'VendorsPerspective\VendorsPerspectiveController@doAction');

        Route::patch('/{accounting_vendors_perspective}', 'VendorsPerspective\VendorsPerspectiveController@update');
        Route::delete('/{accounting_vendors_perspective}', 'VendorsPerspective\VendorsPerspectiveController@destroy');
    });

Route::prefix('weekly-paid-invoices-performance')->group(function () {
        Route::get('/', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@index');
        Route::get('/actions', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@getActions');

Route::get('{awpip}/tags ', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@tags');
Route::post('{awpip}/tags ', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@saveTags');
Route::get('{awpip}/addresses ', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@addresses');
Route::post('{awpip}/addresses ', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@saveAddresses');

        Route::get('/{awpip}/{subObjects}', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@relatedObjects');
        Route::get('/{awpip}', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@show');

        Route::post('/', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@store');
        Route::post('/{awpip}/do/{action}', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@doAction');

        Route::patch('/{awpip}', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@update');
        Route::delete('/{awpip}', 'WeeklyPaidInvoicesPerformance\WeeklyPaidInvoicesPerformanceController@destroy');
    });

// EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE






















































        Route::prefix('webhooks')->group(
            function () {
                Route::get('/stripe/{event}', 'PaymentGateways\PaymentGatewayWebhooksController@stripe');
            }
        );


// MULTI-TENANT PARTNERSHIP ROUTES
Route::prefix('partner-programs')->group(function () {
        //  Self service onboarding of a tenant. POST only, no collision with the generated CRUD routes.
        Route::post('/enable', 'PartnerPrograms\\PartnerProgramsController@enable');
        Route::post('/disable', 'PartnerPrograms\\PartnerProgramsController@disable');
    });
Route::prefix('partnerships')->group(function () {
        //  Invitation lifecycle. These are POST only, so they do not collide with the generated CRUD routes.
        Route::post('/invite', 'Partnerships\PartnershipsController@invite');
        //  Applications: a company asks to join another company's program, the tenant approves or rejects.
        Route::post('/apply', 'Partnerships\PartnershipsController@apply');
        Route::post('/{accounting_partnerships}/approve-application', 'Partnerships\PartnershipsController@approveApplication');
        Route::post('/{accounting_partnerships}/reject-application', 'Partnerships\PartnershipsController@rejectApplication');
        Route::post('/{accounting_partnerships}/accept', 'Partnerships\PartnershipsController@accept');
        Route::post('/{accounting_partnerships}/reject', 'Partnerships\PartnershipsController@reject');
        Route::post('/{accounting_partnerships}/revoke', 'Partnerships\PartnershipsController@revoke');
        Route::post('/{accounting_partnerships}/visibility', 'Partnerships\PartnershipsController@visibility');
    });

Route::prefix('partner-programs')->group(function () {
        Route::get('/', 'PartnerPrograms\PartnerProgramsController@index');
        Route::get('/actions', 'PartnerPrograms\PartnerProgramsController@getActions');

Route::get('{accounting_partner_programs}/tags ', 'PartnerPrograms\PartnerProgramsController@tags');
Route::post('{accounting_partner_programs}/tags ', 'PartnerPrograms\PartnerProgramsController@saveTags');
Route::get('{accounting_partner_programs}/addresses ', 'PartnerPrograms\PartnerProgramsController@addresses');
Route::post('{accounting_partner_programs}/addresses ', 'PartnerPrograms\PartnerProgramsController@saveAddresses');

        Route::get('/{accounting_partner_programs}/{subObjects}', 'PartnerPrograms\PartnerProgramsController@relatedObjects');
        Route::get('/{accounting_partner_programs}', 'PartnerPrograms\PartnerProgramsController@show');

        Route::post('/', 'PartnerPrograms\PartnerProgramsController@store');
        Route::post('/{accounting_partner_programs}/do/{action}', 'PartnerPrograms\PartnerProgramsController@doAction');

        Route::patch('/{accounting_partner_programs}', 'PartnerPrograms\PartnerProgramsController@update');
        Route::delete('/{accounting_partner_programs}', 'PartnerPrograms\PartnerProgramsController@destroy');
    });

Route::prefix('partnership-roles')->group(function () {
        Route::get('/', 'PartnershipRoles\PartnershipRolesController@index');
        Route::get('/actions', 'PartnershipRoles\PartnershipRolesController@getActions');

Route::get('{accounting_partnership_roles}/tags ', 'PartnershipRoles\PartnershipRolesController@tags');
Route::post('{accounting_partnership_roles}/tags ', 'PartnershipRoles\PartnershipRolesController@saveTags');
Route::get('{accounting_partnership_roles}/addresses ', 'PartnershipRoles\PartnershipRolesController@addresses');
Route::post('{accounting_partnership_roles}/addresses ', 'PartnershipRoles\PartnershipRolesController@saveAddresses');

        Route::get('/{accounting_partnership_roles}/{subObjects}', 'PartnershipRoles\PartnershipRolesController@relatedObjects');
        Route::get('/{accounting_partnership_roles}', 'PartnershipRoles\PartnershipRolesController@show');

        Route::post('/', 'PartnershipRoles\PartnershipRolesController@store');
        Route::post('/{accounting_partnership_roles}/do/{action}', 'PartnershipRoles\PartnershipRolesController@doAction');

        Route::patch('/{accounting_partnership_roles}', 'PartnershipRoles\PartnershipRolesController@update');
        Route::delete('/{accounting_partnership_roles}', 'PartnershipRoles\PartnershipRolesController@destroy');
    });

Route::prefix('partnership-role-assignments')->group(function () {
        Route::get('/', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@index');
        Route::get('/actions', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@getActions');

Route::get('{partnership_role_assignments}/tags ', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@tags');
Route::post('{partnership_role_assignments}/tags ', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@saveTags');
Route::get('{partnership_role_assignments}/addresses ', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@addresses');
Route::post('{partnership_role_assignments}/addresses ', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@saveAddresses');

        Route::get('/{partnership_role_assignments}/{subObjects}', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@relatedObjects');
        Route::get('/{partnership_role_assignments}', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@show');

        Route::post('/', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@store');
        Route::post('/{partnership_role_assignments}/do/{action}', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@doAction');

        Route::patch('/{partnership_role_assignments}', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@update');
        Route::delete('/{partnership_role_assignments}', 'PartnershipRoleAssignments\PartnershipRoleAssignmentsController@destroy');
    });

Route::prefix('account-partners')->group(function () {
        Route::get('/', 'AccountPartners\AccountPartnersController@index');
        Route::get('/actions', 'AccountPartners\AccountPartnersController@getActions');

Route::get('{accounting_account_partners}/tags ', 'AccountPartners\AccountPartnersController@tags');
Route::post('{accounting_account_partners}/tags ', 'AccountPartners\AccountPartnersController@saveTags');
Route::get('{accounting_account_partners}/addresses ', 'AccountPartners\AccountPartnersController@addresses');
Route::post('{accounting_account_partners}/addresses ', 'AccountPartners\AccountPartnersController@saveAddresses');

        Route::get('/{accounting_account_partners}/{subObjects}', 'AccountPartners\AccountPartnersController@relatedObjects');
        Route::get('/{accounting_account_partners}', 'AccountPartners\AccountPartnersController@show');

        Route::post('/', 'AccountPartners\AccountPartnersController@store');
        Route::post('/{accounting_account_partners}/do/{action}', 'AccountPartners\AccountPartnersController@doAction');

        Route::patch('/{accounting_account_partners}', 'AccountPartners\AccountPartnersController@update');
        Route::delete('/{accounting_account_partners}', 'AccountPartners\AccountPartnersController@destroy');
    });

Route::prefix('partner-program-zones')->group(function () {
        Route::get('/', 'PartnerProgramZones\PartnerProgramZonesController@index');
        Route::get('/actions', 'PartnerProgramZones\PartnerProgramZonesController@getActions');

Route::get('{accounting_partner_program_zones}/tags ', 'PartnerProgramZones\PartnerProgramZonesController@tags');
Route::post('{accounting_partner_program_zones}/tags ', 'PartnerProgramZones\PartnerProgramZonesController@saveTags');
Route::get('{accounting_partner_program_zones}/addresses ', 'PartnerProgramZones\PartnerProgramZonesController@addresses');
Route::post('{accounting_partner_program_zones}/addresses ', 'PartnerProgramZones\PartnerProgramZonesController@saveAddresses');

        Route::get('/{accounting_partner_program_zones}/{subObjects}', 'PartnerProgramZones\PartnerProgramZonesController@relatedObjects');
        Route::get('/{accounting_partner_program_zones}', 'PartnerProgramZones\PartnerProgramZonesController@show');

        Route::post('/', 'PartnerProgramZones\PartnerProgramZonesController@store');
        Route::post('/{accounting_partner_program_zones}/do/{action}', 'PartnerProgramZones\PartnerProgramZonesController@doAction');

        Route::patch('/{accounting_partner_program_zones}', 'PartnerProgramZones\PartnerProgramZonesController@update');
        Route::delete('/{accounting_partner_program_zones}', 'PartnerProgramZones\PartnerProgramZonesController@destroy');
    });

Route::prefix('partnership-zones')->group(function () {
        Route::get('/', 'PartnershipZones\PartnershipZonesController@index');
        Route::get('/actions', 'PartnershipZones\PartnershipZonesController@getActions');

Route::get('{accounting_partnership_zones}/tags ', 'PartnershipZones\PartnershipZonesController@tags');
Route::post('{accounting_partnership_zones}/tags ', 'PartnershipZones\PartnershipZonesController@saveTags');
Route::get('{accounting_partnership_zones}/addresses ', 'PartnershipZones\PartnershipZonesController@addresses');
Route::post('{accounting_partnership_zones}/addresses ', 'PartnershipZones\PartnershipZonesController@saveAddresses');

        Route::get('/{accounting_partnership_zones}/{subObjects}', 'PartnershipZones\PartnershipZonesController@relatedObjects');
        Route::get('/{accounting_partnership_zones}', 'PartnershipZones\PartnershipZonesController@show');

        Route::post('/', 'PartnershipZones\PartnershipZonesController@store');
        Route::post('/{accounting_partnership_zones}/do/{action}', 'PartnershipZones\PartnershipZonesController@doAction');

        Route::patch('/{accounting_partnership_zones}', 'PartnershipZones\PartnershipZonesController@update');
        Route::delete('/{accounting_partnership_zones}', 'PartnershipZones\PartnershipZonesController@destroy');
    });

Route::prefix('invoice-commissions')->group(function () {
        Route::get('/', 'InvoiceCommissions\InvoiceCommissionsController@index');
        Route::get('/actions', 'InvoiceCommissions\InvoiceCommissionsController@getActions');

Route::get('{accounting_invoice_commissions}/tags ', 'InvoiceCommissions\InvoiceCommissionsController@tags');
Route::post('{accounting_invoice_commissions}/tags ', 'InvoiceCommissions\InvoiceCommissionsController@saveTags');
Route::get('{accounting_invoice_commissions}/addresses ', 'InvoiceCommissions\InvoiceCommissionsController@addresses');
Route::post('{accounting_invoice_commissions}/addresses ', 'InvoiceCommissions\InvoiceCommissionsController@saveAddresses');

        Route::get('/{accounting_invoice_commissions}/{subObjects}', 'InvoiceCommissions\InvoiceCommissionsController@relatedObjects');
        Route::get('/{accounting_invoice_commissions}', 'InvoiceCommissions\InvoiceCommissionsController@show');

        Route::post('/', 'InvoiceCommissions\InvoiceCommissionsController@store');
        Route::post('/{accounting_invoice_commissions}/do/{action}', 'InvoiceCommissions\InvoiceCommissionsController@doAction');

        Route::patch('/{accounting_invoice_commissions}', 'InvoiceCommissions\InvoiceCommissionsController@update');
        Route::delete('/{accounting_invoice_commissions}', 'InvoiceCommissions\InvoiceCommissionsController@destroy');
    });

    }
);



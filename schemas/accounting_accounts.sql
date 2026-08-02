-- PostgreSQL
-- TABLE (reconstructed: nextdeveloper/accounting shipped no schemas/ files;
-- built from the database.leo.v4 reference DDL, corrected to match the current
-- NextDeveloper\Accounting\Database\Models\Accounts fillable/casts list:
-- accounting_id -> accounting_identifier, trade_office_name -> trade_office,
-- added balance/is_disabled/is_suspended/partner_code/tags which the model has
-- but the legacy DDL didn't.)

create table if not exists accounting_accounts
(
    id                    bigserial primary key,
    uuid                  uuid           default gen_random_uuid(),

    iam_account_id        bigint                                   not null unique,

    tax_office            text                                     null,
    tax_number            text                                     null,

    accounting_identifier text                                     null,

    credit                numeric(10, 2) default 0,
    balance               numeric(10, 2) default 0,
    common_currency_id    bigint                                   not null,

    distributor_id        bigint                                   null,
    sales_partner_id      bigint                                   null,
    integrator_partner_id bigint                                   null,
    affiliate_partner_id  bigint                                   null,
    partner_code          text                                     null,

    trade_office_number   text                                     null,
    trade_office          text                                     null,

    tr_mersis             text                                     null,

    is_reseller           boolean        default false,
    is_integrator         boolean        default false,
    is_vendor             boolean        default false,
    is_distributor        boolean        default false,
    is_affiliate          boolean        default false,
    is_suspended          boolean        default false,
    is_disabled           boolean        default false,

    affiliate_level       integer        default 1,

    mapping               json           default null,
    tags                  text[]         not null default '{}',

    created_at            timestamptz    default CURRENT_TIMESTAMP not null,
    updated_at            timestamptz    default CURRENT_TIMESTAMP not null,
    deleted_at            timestamptz                              null
);

comment on column accounting_accounts.iam_account_id IS '[ro]';
comment on column accounting_accounts.distributor_id is '[alias:accounting_account_id]';
comment on column accounting_accounts.sales_partner_id is '[alias:accounting_account_id]';
comment on column accounting_accounts.integrator_partner_id is '[alias:accounting_account_id]';
comment on column accounting_accounts.affiliate_partner_id is '[alias:accounting_account_id]';

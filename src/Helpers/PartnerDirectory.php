<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * The partner directory: the partner lists (distributors / integrators / sales partners / affiliates / vendors
 * perspectives) that every account can open.
 *
 * Two rules live here:
 *  1. WHO is listed: scope() limits the list to the partners the viewer may see, using the visibility chosen on the
 *     partnership (private / customers / public).
 *  2. WHAT is shown: stripPrivateFields() removes financial and tax data of a partner company. A directory entry is
 *     a business card, it must never carry the credit balance or tax numbers of the partner.
 */
class PartnerDirectory
{
    /**
     * Fields that must never be part of a directory entry.
     */
    public const PRIVATE_FIELDS = [
        'credit',
        'tax_number',
        'tax_office',
        'accounting_identifier',
        'tr_mersis',
        'trade_office',
        'trade_office_number',
    ];

    /**
     * Limits a partner perspective query to the partners the current viewer may see.
     *
     * Visible are the partners that:
     *  - are public, or
     *  - are the viewer's own account, or
     *  - belong to the tenant the viewer acts as (the tenant sees its own partners), or
     *  - are 'customers' visible and serve the viewer (an open accounting_account_partners link), or
     *  - have no partnership row at all (legacy partners that were never migrated keep the old behaviour, they are
     *    visible to everybody until the data fix creates their partnership row).
     *
     * The queries are plain DB queries on purpose: going through the models would re-enter AuthorizationScope.
     * Platform roles (accounting-admin / accounting-manager) do not call this and keep the full list.
     */
    public static function scope(Builder $builder): void
    {
        try {
            $me = AccountingHelper::getAccount()->id;
        } catch (\Throwable $e) {
            //  No accounting account yet: nothing is "mine" and nobody serves me.
            $me = 0;
        }

        $tenant = UserHelper::currentAccount()->id;

        $builder->where(function (Builder $q) use ($me, $tenant) {
            //  Legacy partners without a partnership row. whereNotNull: a NULL inside NOT IN would hide every row.
            $q->whereNotIn(
                'id',
                DB::table('accounting_partnerships')
                    ->whereNull('deleted_at')
                    ->whereNotNull('accounting_account_id')
                    ->select('accounting_account_id')
            )->orWhereIn(
                'id',
                DB::table('accounting_partnerships as p')
                    ->whereNull('p.deleted_at')
                    ->where('p.status', 'active')
                    ->where(function ($visible) use ($me, $tenant) {
                        $visible->where('p.visibility', 'public')
                            ->orWhere('p.accounting_account_id', $me)
                            ->orWhere('p.tenant_account_id', $tenant)
                            ->orWhere(function ($customers) use ($me) {
                                $customers->where('p.visibility', 'customers')
                                    ->whereExists(function ($served) use ($me) {
                                        $served->select(DB::raw(1))
                                            ->from('accounting_account_partners as ap')
                                            ->whereColumn('ap.partnership_id', 'p.id')
                                            ->where('ap.accounting_account_id', $me)
                                            ->whereNull('ap.finished_at')
                                            ->whereNull('ap.deleted_at');
                                    });
                            });
                    })
                    ->select('p.accounting_account_id')
            );
        });
    }

    /**
     * Removes the private fields from a transformed directory entry (see PRIVATE_FIELDS).
     */
    public static function stripPrivateFields(array $transformed): array
    {
        return array_diff_key($transformed, array_flip(self::PRIVATE_FIELDS));
    }
}

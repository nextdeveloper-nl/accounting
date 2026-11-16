<?php

namespace NextDeveloper\Accounting\Services;

use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\AccountPartners;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\InvoiceCommissions;
use NextDeveloper\Accounting\Database\Models\Invoices;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractInvoiceCommissionsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Multi-tenant commissions: turns a PAID invoice of a customer into commission invoices for the partners that serve
 * that customer, with the roles and rates of the customer's tenant.
 *
 * Replaces the four fixed commission jobs (distributor / integrator / reseller / affiliate with hardcoded rates):
 *  - who earns: every partner that has an OPEN customer link (accounting_account_partners) for the customer at the
 *    time the invoice was created, one commission per role, all roles pay (this is how the four jobs behaved);
 *  - how much: invoice amount x the role's commission_rate;
 *  - hierarchy: the DIRECT parent of a partner additionally earns invoice amount x the role's parent_override_rate
 *    (level 1). It does not reduce what the partner earns. Deeper ancestors earn nothing (v1);
 *  - once only: every (invoice, role, partner, level) is recorded in accounting_invoice_commissions, which has a unique
 *    index, so running it again (or twice at the same time) cannot pay twice. For the four legacy roles the legacy
 *    column on the invoice (e.g. reseller_commission_invoice_id) is respected and still written, so the old jobs and
 *    the new service never pay the same invoice twice during the transition.
 *
 * Not handled (same as before): the commission invoice is in the currency of the parent invoice, also when the partner
 * works in another currency.
 */
class PartnerCommissionsService
{
    /**
     * Role slug -> column on accounting_invoices that the four legacy jobs used as their "already done" marker.
     */
    private const LEGACY_COLUMNS = [
        'distributor' => 'distributor_commission_invoice_id',
        'integrator'  => 'integrator_commission_invoice_id',
        'reseller'    => 'reseller_commission_invoice_id',
        'affiliate'   => 'affiliate_commission_invoice_id',
    ];

    /**
     * Generates the commissions of one invoice.
     *
     * @param Invoices $invoice The (paid, non commission) invoice of the customer
     * @param bool     $dryRun  Only report what would be created, change nothing
     *
     * @return array One row per commission: status (created | would_create | already_done | skipped | error) plus details
     */
    public static function generateForInvoice(Invoices $invoice, bool $dryRun = false): array
    {
        //  The jobs run without a logged in user. Writes need the system identity.
        return UserHelper::runAsAdmin(fn() => self::process($invoice, $dryRun));
    }

    /**
     * Commission amount, rounded to 4 decimals.
     */
    public static function calculateAmount(float $invoiceAmount, float $rate): float
    {
        return round($invoiceAmount * $rate, 4);
    }

    /**
     * Does a customer link cover the moment the invoice was created? It starts at started_at and, when it was ended
     * (partner revoked or replaced), stops at finished_at. An invoice issued while the link was open earns its
     * commission even if the link ended afterwards.
     */
    public static function linkCoversInvoice(?Carbon $startedAt, ?Carbon $finishedAt, Carbon $invoiceCreatedAt): bool
    {
        if ($startedAt && $invoiceCreatedAt->lt($startedAt)) {
            return false;
        }

        if ($finishedAt && $invoiceCreatedAt->gte($finishedAt)) {
            return false;
        }

        return true;
    }

    /**
     * Checks the rates of everything that would be paid for ONE invoice. Returns the reason when something is out of
     * bounds, null when it is fine.
     *
     * A role rate comes from tenant managed data (a manager can type any number), and every commission becomes a
     * payable invoice, so the engine does not trust it: each rate must be within 0..$maxEach and all the rates that
     * would be paid for the invoice together within $maxTotal. When a limit is broken NOTHING is paid for the invoice
     * (no partial payout), the problem is reported and a person has to look at the role setup.
     *
     * @param float[] $rates every rate (own commission and parent override) that would be paid for the invoice
     */
    public static function ratesOutOfBounds(array $rates, float $maxEach, float $maxTotal): ?string
    {
        foreach ($rates as $rate) {
            if ($rate < 0 || $rate > $maxEach) {
                return 'a rate of ' . round($rate * 100, 2) . '% is outside the allowed range of 0% to ' . round($maxEach * 100, 2) . '%';
            }
        }

        $total = array_sum($rates);

        if ($total > $maxTotal) {
            return 'the total of ' . round($total * 100, 2) . '% exceeds the allowed ' . round($maxTotal * 100, 2) . '% payout for one invoice';
        }

        return null;
    }

    private static function process(Invoices $invoice, bool $dryRun): array
    {
        if (!$invoice->is_paid || $invoice->is_commission_invoice) {
            return [];
        }

        if ((float)$invoice->amount <= 0) {
            return [];
        }

        $customer = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $invoice->accounting_account_id)
            ->first();

        if (!$customer) {
            return [];
        }

        //  A customer without a tenant belongs to the default (platform) tenant: accounts that are created outside the
        //  signup flow do not get a tenant right away.
        $tenantId = $customer->tenant_account_id ?: AccountPartnersService::getDefaultTenantId();

        $links = AccountPartners::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $customer->id)
            ->where('tenant_account_id', $tenantId)
            ->get()
            ->filter(fn($link) => self::linkCoversInvoice($link->started_at, $link->finished_at, $invoice->created_at));

        $results = [];
        //  Everything that would be paid is collected first, checked as a whole, and only then created.
        $plan = [];

        foreach ($links as $link) {
            $role = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)->where('id', $link->partnership_role_id)->first();
            $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)->where('id', $link->partnership_id)->first();

            //  Data that does not hang together is skipped and reported, never paid.
            if (!$role || !$partnership
                || (int)$role->tenant_account_id !== (int)$link->tenant_account_id
                || (int)$partnership->tenant_account_id !== (int)$link->tenant_account_id) {
                $results[] = self::row('skipped', $invoice, $link, $role, $partnership, 0, 0, 'link, role and partnership do not belong to the same tenant');
                continue;
            }

            if ($role->status !== 'active') {
                $results[] = self::row('skipped', $invoice, $link, $role, $partnership, 0, 0, 'role is not active');
                continue;
            }

            //  An open link on a partner that is not active anymore (revoked / rejected / pending) earns nothing. A link
            //  that was closed AFTER the invoice was created still earns, the partner served the customer at that time.
            if ($link->finished_at === null && $partnership->status !== 'active') {
                $results[] = self::row('skipped', $invoice, $link, $role, $partnership, 0, 0, 'partnership is not active');
                continue;
            }

            $rate = (float)$role->commission_rate;

            if ($rate > 0) {
                $plan[] = [$link, $role, $partnership, 0, $rate];
            }

            //  The direct parent earns the parent override of this role on top of the partner's own commission.
            $overrideRate = (float)$role->parent_override_rate;

            if ($overrideRate > 0 && $partnership->parent_partnership_id) {
                $parent = Partnerships::withoutGlobalScope(AuthorizationScope::class)
                    ->where('id', $partnership->parent_partnership_id)
                    ->first();

                if ($parent && $parent->status === 'active' && (int)$parent->tenant_account_id === (int)$link->tenant_account_id) {
                    $plan[] = [$link, $role, $parent, 1, $overrideRate];
                }
            }
        }

        //  Bounds on the rates (see ratesOutOfBounds). Defaults: no single rate above 50%, no more than 50% paid in
        //  total for one invoice; both can be changed in config accounting.partnership.*.
        $problem = self::ratesOutOfBounds(
            array_map(fn($item) => $item[4], $plan),
            (float)config('accounting.partnership.max_commission_rate', 0.5),
            (float)config('accounting.partnership.max_total_commission_rate', 0.5)
        );

        if ($problem !== null) {
            Log::error('[PartnerCommissionsService] Commission rates out of bounds, nothing paid for invoice ' . $invoice->id . ': ' . $problem);

            $results[] = self::row('error', $invoice, null, null, null, 0, 0, 'nothing paid for this invoice: ' . $problem);

            return $results;
        }

        foreach ($plan as [$link, $role, $earner, $level, $rate]) {
            $results[] = self::commission($invoice, $customer, $link, $role, $earner, $level, $rate, $dryRun);
        }

        return $results;
    }

    /**
     * Creates (or reports) one commission: the record in accounting_invoice_commissions and the commission invoice of
     * the partner, in one transaction.
     */
    private static function commission(Invoices $invoice, Accounts $customer, AccountPartners $link, PartnershipRoles $role, Partnerships $earner, int $level, float $rate, bool $dryRun): array
    {
        $legacyColumn = $level === 0 ? (self::LEGACY_COLUMNS[$role->slug] ?? null) : null;

        //  The four legacy jobs marked the invoice themselves; if they already paid this role, do not pay twice.
        if ($legacyColumn && $invoice->{$legacyColumn}) {
            return self::row('already_done', $invoice, $link, $role, $earner, $level, $rate, 'paid by the legacy job');
        }

        $alreadyRecorded = InvoiceCommissions::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_invoice_id', $invoice->id)
            ->where('partnership_role_id', $role->id)
            ->where('partnership_id', $earner->id)
            ->where('level', $level)
            ->exists();

        if ($alreadyRecorded) {
            return self::row('already_done', $invoice, $link, $role, $earner, $level, $rate);
        }

        $amount = self::calculateAmount((float)$invoice->amount, $rate);

        if ($amount <= 0) {
            return self::row('skipped', $invoice, $link, $role, $earner, $level, $rate, 'commission amount is zero');
        }

        $partnerAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $earner->accounting_account_id)
            ->first();

        if (!$partnerAccount) {
            return self::row('skipped', $invoice, $link, $role, $earner, $level, $rate, 'accounting account of the partner not found');
        }

        if ($dryRun) {
            return self::row('would_create', $invoice, $link, $role, $earner, $level, $rate, null, $amount);
        }

        try {
            return DB::transaction(function () use ($invoice, $link, $role, $earner, $level, $rate, $amount, $partnerAccount, $legacyColumn) {
                //  Reserve first: the unique index (invoice, role, partner, level) makes a concurrent second run fail here,
                //  before any commission invoice exists.
                $record = AbstractInvoiceCommissionsService::create([
                    'tenant_account_id'     => $link->tenant_account_id,
                    'accounting_invoice_id' => $invoice->id,
                    'partnership_role_id'   => $role->id,
                    'partnership_id'        => $earner->id,
                    'level'                 => $level,
                ]);

                $commissionInvoice = InvoicesService::create([
                    //  Unique per commission (the old YmdHis alone repeated within the same second).
                    'invoice_number'        => now()->format('YmdHis') . '-' . $record->id,
                    'accounting_account_id' => $partnerAccount->id,
                    'parent_invoice_id'     => $invoice->id,
                    'amount'                => $amount,
                    'exchange_rate'         => $invoice->exchange_rate,
                    'common_currency_id'    => $invoice->common_currency_id,
                    'is_commission_invoice' => true,
                    'vat'                   => '0',
                    'is_paid'               => false,
                    'is_sealed'             => true,
                    'is_payable'            => true,
                    'note'                  => sprintf(
                        'Commission invoice (%s - %s%%%s) for invoice: %s',
                        $role->name,
                        rtrim(rtrim(number_format($rate * 100, 2, '.', ''), '0'), '.'),
                        $level > 0 ? ', parent override' : '',
                        $invoice->uuid
                    ),
                ]);

                $record->update(['commission_invoice_id' => $commissionInvoice->id]);

                //  Keep the legacy marker in step while the old jobs still exist.
                if ($legacyColumn) {
                    $invoice->update([$legacyColumn => $commissionInvoice->id]);
                }

                Log::info('[PartnerCommissionsService] Commission invoice created', [
                    'invoice_id'            => $invoice->id,
                    'commission_invoice_id' => $commissionInvoice->id,
                    'partnership_id'        => $earner->id,
                    'role'                  => $role->slug,
                    'level'                 => $level,
                    'amount'                => $amount,
                ]);

                return self::row('created', $invoice, $link, $role, $earner, $level, $rate, null, $amount, $commissionInvoice->id);
            });
        } catch (QueryException $e) {
            //  23505 = unique violation: another run recorded this commission between our check and our insert.
            if ($e->getCode() === '23505') {
                return self::row('already_done', $invoice, $link, $role, $earner, $level, $rate);
            }

            Log::error('[PartnerCommissionsService] Commission failed', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);

            return self::row('error', $invoice, $link, $role, $earner, $level, $rate, $e->getMessage());
        } catch (\Throwable $e) {
            //  Never dd()/die inside a queue worker. The caller sees the error row and the other commissions go on.
            Log::error('[PartnerCommissionsService] Commission failed', ['invoice_id' => $invoice->id, 'error' => $e->getMessage()]);

            return self::row('error', $invoice, $link, $role, $earner, $level, $rate, $e->getMessage());
        }
    }

    private static function row(string $status, Invoices $invoice, $link, $role, $earner, int $level, float $rate, ?string $reason = null, float $amount = 0, ?int $commissionInvoiceId = null): array
    {
        return [
            'status'                => $status,
            'invoice_id'            => $invoice->id,
            'role'                  => $role?->slug,
            'partnership_id'        => $earner?->id,
            'level'                 => $level,
            'rate'                  => $rate,
            'amount'                => $amount,
            'commission_invoice_id' => $commissionInvoiceId,
            'reason'                => $reason,
        ];
    }
}

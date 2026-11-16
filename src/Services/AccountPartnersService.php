<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Services\AbstractServices\AbstractAccountPartnersService;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Helpers\PartnershipTenantGuard;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use NextDeveloper\Accounting\Database\Models\Accounts as AccountingAccounts;
use NextDeveloper\Accounting\Database\Models\AccountPartners;
use NextDeveloper\Accounting\Database\Models\PartnershipRoleAssignments;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use Illuminate\Support\Facades\Log;
use NextDeveloper\IAM\Database\Models\Accounts as IamAccounts;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for AccountPartners
 *
 * Class AccountPartnersService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class AccountPartnersService extends AbstractAccountPartnersService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    /**
     * Multi-tenant partnership: the tenant (program owner) is NEVER taken from the request. It is always the
     * account the current user is acting as, so a client cannot create rows inside another tenant.
     *
     * @param  array $data
     * @return mixed
     */
    public static function create(array $data)
    {
        //  Every referenced partnership / role / program / customer must belong to OUR tenant (uuids are checked
        //  before they are converted to ids, independent of what the role scope shows).
        PartnershipTenantGuard::assertReferences($data);

        //  A partner that has not accepted (pending), rejected or was revoked must not be given customers.
        if (!empty($data['partnership_id'])) {
            $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
                ->where('uuid', $data['partnership_id'])
                ->first();

            if (!$partnership || $partnership->status !== 'active') {
                throw new NotAllowedException('Customers can only be assigned to an active partner.');
            }
        }

        unset($data['tenant_account_id']);
        $data['tenant_account_id'] = UserHelper::currentAccount()->id;

        return parent::create($data);
    }

    /**
     * Multi-tenant partnership: the tenant of an existing row can never be changed through the API.
     *
     * @param  $id
     * @param  array $data
     * @return mixed
     */
    public static function update($id, array $data)
    {
        unset($data['tenant_account_id']);

        //  Same ownership check on update, otherwise a row could be re-pointed to another tenant's objects.
        PartnershipTenantGuard::assertReferences($data);

        return parent::update($id, $data);
    }

    /**
     * Customers of the tenant for the "link a customer to a partner" picker.
     *
     * The tenant cannot list its customers through the accounts API (those lists only show the viewer's own account),
     * so this returns them explicitly: only accounts that belong to the acting tenant (accounting_accounts.tenant_account_id),
     * whose name starts with the typed text, at least 3 characters, 10 results, hourly limit. Only partnership managers
     * (or platform admins) can call it. The returned id is the ACCOUNTING account id (uuid), which is what
     * /account-partners expects as accounting_account_id.
     *
     * @return array [{id, name}]
     * @throws NotAllowedException
     */
    public static function findCustomerCandidates(string $query): array
    {
        if (!(UserHelper::hasRole('partnership-manager') || UserHelper::hasRole('accounting-admin') || UserHelper::hasRole('system-admin'))) {
            throw new NotAllowedException('You are not allowed to manage the partner program.');
        }

        $tenant = UserHelper::currentAccount();
        $query = trim($query);

        if (mb_strlen($query) < 3 || mb_strlen($query) > 100) {
            throw new NotAllowedException('Type at least 3 characters to search for a customer.');
        }

        if (RateLimiter::hit('partnership-customers:tenant:' . $tenant->id, 3600) > (int)config('accounting.partnership.max_candidate_searches_per_hour', 60)) {
            throw new NotAllowedException('You searched too often. Please try again later.');
        }

        //  Literal prefix: a typed % or _ must not widen the search.
        $prefix = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $query) . '%';

        $customers = AccountingAccounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $tenant->id)
            ->whereIn('iam_account_id', IamAccounts::withoutGlobalScope(AuthorizationScope::class)
                ->where('name', 'ilike', $prefix)
                ->select('id'))
            ->limit(10)
            ->get();

        $names = IamAccounts::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('id', $customers->pluck('iam_account_id'))
            ->pluck('name', 'id');

        return $customers->map(fn($customer) => [
            'id'   => $customer->uuid,
            'name' => $names[$customer->iam_account_id] ?? null,
        ])->sortBy('name')->values()->all();
    }

    // ---------------------------------------------------------------------------------------------------------
    // Multi-tenant signup and the bridge to the legacy partner columns
    //
    // A customer belongs to ONE tenant (accounting_accounts.tenant_account_id). Customers who sign up with a partner
    // code join the tenant that owns the code; everybody else belongs to the default (platform) tenant. A customer is
    // served by partners through accounting_account_partners (one open link per role). The four legacy pointer columns
    // (distributor_id, sales_partner_id, integrator_partner_id, affiliate_partner_id) are still written, the legacy
    // commission jobs, the statistics and the old screens read them.
    // ---------------------------------------------------------------------------------------------------------

    /**
     * Legacy pointer column -> role slug of the PlusClouds default roles.
     */
    public const LEGACY_POINTERS = [
        'distributor_id'        => 'distributor',
        'sales_partner_id'      => 'reseller',
        'integrator_partner_id' => 'integrator',
        'affiliate_partner_id'  => 'affiliate',
    ];

    /**
     * The tenant of customers that did not come with a partner code: the platform itself (the account of the leo
     * owner), or the account set in config accounting.partnership.default_tenant_account_id.
     */
    public static function getDefaultTenantId(): int
    {
        $configured = config('accounting.partnership.default_tenant_account_id');

        return $configured ? (int)$configured : (int)UserHelper::getLeoOwnerAccount()->id;
    }

    /**
     * Gives a customer a tenant. A customer that already has one keeps it: one tenant per customer.
     *
     * @param int|null $tenantAccountId IAM account id of the tenant; null = the default tenant
     */
    public static function ensureTenant(AccountingAccounts $customer, ?int $tenantAccountId = null): AccountingAccounts
    {
        if ($customer->tenant_account_id) {
            return $customer;
        }

        $customer->tenant_account_id = $tenantAccountId ?? self::getDefaultTenantId();
        $customer->saveQuietly();

        return $customer;
    }

    /**
     * Picks the role a signup code grants: among the roles the partner holds, the active roles of the tenant that are
     * flagged is_default_for_signup_code. The role with the highest priority wins, then the lowest id.
     *
     * @param array $roles          [['id' => int, 'priority' => int, 'is_default_for_signup_code' => bool, 'status' => string]]
     * @param array $heldRoleIds    ids of the roles the partner holds (active assignments)
     * @return int|null role id, null when the partner holds no signup role
     */
    public static function pickSignupRoleId(array $roles, array $heldRoleIds): ?int
    {
        $candidates = array_filter($roles, fn($role) => $role['is_default_for_signup_code']
            && $role['status'] === 'active'
            && in_array($role['id'], $heldRoleIds, true));

        if (!$candidates) {
            return null;
        }

        usort($candidates, fn($a, $b) => [$b['priority'], $a['id']] <=> [$a['priority'], $b['id']]);

        return $candidates[0]['id'];
    }

    /**
     * Signup with a partner code: the customer joins the tenant that owns the code and is linked to the partner under
     * the tenant's signup role (the role flagged is_default_for_signup_code that the partner holds).
     *
     * Nothing happens (and the customer stays with the platform) when the code does not lead to an active partnership,
     * the partner holds no signup role, the customer already belongs to another tenant, or the code is the customer's own.
     *
     * @return AccountPartners|null the new link, null when the customer was not attached to the partner
     */
    public static function assignFromSignupCode(AccountingAccounts $customer, Partnerships $partnership): ?AccountPartners
    {
        if ($partnership->status !== 'active' || !$partnership->tenant_account_id) {
            return null;
        }

        //  Referring yourself is not a referral.
        if ((int)$partnership->accounting_account_id === (int)$customer->id) {
            return null;
        }

        //  One tenant per customer: a customer that already belongs to another tenant is not moved.
        if ($customer->tenant_account_id && (int)$customer->tenant_account_id !== (int)$partnership->tenant_account_id) {
            Log::warning('[AccountPartnersService] Signup code of tenant ' . $partnership->tenant_account_id
                . ' ignored, customer ' . $customer->id . ' already belongs to tenant ' . $customer->tenant_account_id);

            return null;
        }

        $roles = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $partnership->tenant_account_id)
            ->get()
            ->map(fn($role) => [
                'id'                         => (int)$role->id,
                'priority'                   => (int)$role->priority,
                'is_default_for_signup_code' => (bool)$role->is_default_for_signup_code,
                'status'                     => $role->status,
            ])->all();

        $held = PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
            ->where('partnership_id', $partnership->id)
            ->where('status', 'active')
            ->whereNull('finished_at')
            ->pluck('partnership_role_id')
            ->map(fn($id) => (int)$id)
            ->all();

        $roleId = self::pickSignupRoleId($roles, $held);

        if ($roleId === null) {
            Log::info('[AccountPartnersService] Partner ' . $partnership->id . ' holds no signup role, customer '
                . $customer->id . ' stays with the platform.');

            return null;
        }

        $role = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)->where('id', $roleId)->first();

        self::ensureTenant($customer, (int)$partnership->tenant_account_id);

        $link = self::openLink($customer, $role, $partnership);

        //  Legacy pointer of this role, only when it is still empty (a signup code never takes over a customer that
        //  already has a partner for that role).
        $column = array_search($role->slug, self::LEGACY_POINTERS, true);

        if ($column !== false && !$customer->{$column}) {
            $customer->{$column} = $partnership->accounting_account_id;
            $customer->saveQuietly();
        }

        return $link;
    }

    /**
     * Keeps accounting_account_partners in step when a legacy pointer column is set or cleared the old way
     * (default providers, manual assignment). Call it right after the pointer column was written.
     *
     * The link is created in the customer's tenant (the default tenant when the customer has none yet). When the
     * partner account has no ACTIVE partnership in that tenant, or the tenant has no role with that slug, nothing can
     * be linked: it is logged and the pointer stays as it is, so the legacy flow keeps working.
     *
     * @param string               $roleSlug       distributor | reseller | integrator | affiliate (see LEGACY_POINTERS)
     * @param AccountingAccounts|null $partnerAccount the accounting account the pointer now points to, null = cleared
     */
    public static function syncLegacyPartner(AccountingAccounts $customer, string $roleSlug, ?AccountingAccounts $partnerAccount): ?AccountPartners
    {
        self::ensureTenant($customer);

        $tenantId = (int)$customer->tenant_account_id;

        $role = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $tenantId)
            ->where('slug', $roleSlug)
            ->where('status', 'active')
            ->first();

        if (!$role) {
            Log::info('[AccountPartnersService] Tenant ' . $tenantId . ' has no active role "' . $roleSlug . '", no link written.');

            return null;
        }

        //  The pointer was cleared or points elsewhere: the previous partner of this role no longer serves the customer.
        if (!$partnerAccount) {
            self::closeOpenLinks($customer, $role);

            return null;
        }

        $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $tenantId)
            ->where('accounting_account_id', $partnerAccount->id)
            ->where('status', 'active')
            ->first();

        if (!$partnership) {
            //  e.g. the platform account itself serving a customer directly: nothing to link, and the old link (if any)
            //  to a different partner is closed, the customer is no longer served by that one.
            self::closeOpenLinks($customer, $role);

            Log::info('[AccountPartnersService] Account ' . $partnerAccount->id . ' has no active partnership in tenant '
                . $tenantId . ', no link written for customer ' . $customer->id . '.');

            return null;
        }

        return self::openLink($customer, $role, $partnership);
    }

    /**
     * Makes sure the customer has exactly one open link for the role, and that it points to the given partnership.
     * An open link to another partner of the same role is closed first. Idempotent.
     */
    private static function openLink(AccountingAccounts $customer, PartnershipRoles $role, Partnerships $partnership): AccountPartners
    {
        $open = AccountPartners::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $customer->id)
            ->where('partnership_role_id', $role->id)
            ->whereNull('finished_at')
            ->get();

        foreach ($open as $link) {
            if ((int)$link->partnership_id === (int)$partnership->id) {
                return $link;
            }
        }

        self::closeOpenLinks($customer, $role);

        //  Written through the abstract service: the concrete create() takes the tenant from the acting user, here the
        //  tenant is the customer's. The role check is bypassed because signup runs before the customer has any role.
        return UserHelper::withRolesCheckBypassed(fn() => AbstractAccountPartnersService::create([
            'tenant_account_id'     => $customer->tenant_account_id,
            'accounting_account_id' => $customer->id,
            'partnership_role_id'   => $role->id,
            'partnership_id'        => $partnership->id,
            'started_at'            => now(),
        ]));
    }

    private static function closeOpenLinks(AccountingAccounts $customer, PartnershipRoles $role): void
    {
        $links = AccountPartners::withoutGlobalScope(AuthorizationScope::class)
            ->where('accounting_account_id', $customer->id)
            ->where('partnership_role_id', $role->id)
            ->whereNull('finished_at')
            ->get();

        foreach ($links as $link) {
            UserHelper::withRolesCheckBypassed(fn() => $link->update(['finished_at' => now()]));
        }
    }
}

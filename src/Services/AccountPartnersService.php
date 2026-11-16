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
}

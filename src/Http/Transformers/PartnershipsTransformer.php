<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use NextDeveloper\Accounting\Database\Models\PartnershipRoleAssignments;
use NextDeveloper\Accounting\Helpers\PartnershipBranding;
use NextDeveloper\Accounting\Database\Models\Accounts as AccountingAccounts;
use NextDeveloper\Commons\Database\Models\Addresses;
use NextDeveloper\Commons\Database\Models\Countries;
use NextDeveloper\Commons\Database\Models\Domains;
use NextDeveloper\Commons\Database\Models\PhoneNumbers;
use NextDeveloper\IAM\Database\Models\Accounts as IamAccounts;
use NextDeveloper\IAM\Database\Models\Users as IamUsers;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use NextDeveloper\Commons\Common\Cache\CacheHelper;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Commons\Http\Transformers\AbstractTransformer;
use NextDeveloper\Accounting\Http\Transformers\AbstractTransformers\AbstractPartnershipsTransformer;

/**
 * Class PartnershipsTransformer. This class is being used to manipulate the data we are serving to the customer
 *
 * @package NextDeveloper\Accounting\Http\Transformers
 */
class PartnershipsTransformer extends AbstractPartnershipsTransformer
{

    /**
     * The base payload is cached per row and shared by every viewer, so nothing that depends on WHO is looking may be
     * in it. Viewer dependent data (what can I do with this partnership) and related lists are includes instead:
     * ?include=permissions,roles,stats. Includes are built on every request and are never cached.
     */
    protected array $availableIncludes = [
        'states',
        'actions',
        'media',
        'comments',
        'votes',
        'socialMedia',
        'phoneNumbers',
        'addresses',
        'meta',
        'permissions',
        'roles',
        'stats',
        'branding',
        'child_roles',
        'partner_profile',
    ];

    /**
     * What the current user can do with this partnership, so the UI does not have to re-implement the rules.
     * These flags only drive the UI; the services check the same rules again when the action is executed.
     */
    public function includePermissions(Partnerships $model)
    {
        $currentAccountId = (int)UserHelper::currentAccount()->id;
        $isPlatform = UserHelper::hasRole('system-admin') || UserHelper::hasRole('accounting-admin');
        $isManager = UserHelper::hasRole('partnership-manager');

        //  The viewer acts as the tenant that owns the partnership.
        $isTenant = (int)$model->tenant_account_id === $currentAccountId;
        //  The viewer acts as the invited / partner company.
        $isPartner = (int)$model->iam_account_id === $currentAccountId;
        $isOwner = $isPartner && (int)UserHelper::currentAccount()->iam_user_id === (int)UserHelper::me()->id;

        $canAnswer = $model->status === 'pending' && $isPartner && ($isOwner || $isManager || $isPlatform);
        $canManage = $isTenant && ($isManager || $isPlatform);

        //  Can this partner have sub-partners: it is active and one of its active roles may invite sub-partners.
        $canHaveSubPartners = false;
        if ($model->status === 'active') {
            $roles = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
                ->whereIn('id', PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
                    ->where('partnership_id', $model->id)
                    ->where('status', 'active')
                    ->whereNull('finished_at')
                    ->select('partnership_role_id'))
                ->get();

            foreach ($roles as $role) {
                if (!empty($role->permissions['can_invite_sub_partners'])) {
                    $canHaveSubPartners = true;
                    break;
                }
            }
        }

        return $this->item($model, fn() => [
            'can_accept'              => $canAnswer,
            'can_reject'              => $canAnswer,
            'can_revoke'              => $canManage && in_array($model->status, ['pending', 'active']),
            'can_set_visibility'      => $canManage,
            'can_manage_roles'        => $canManage,
            'can_have_sub_partners'   => $canHaveSubPartners,
            //  Only the TENANT adds sub-partners under a partner. A partner inviting on its own is not supported: an
            //  invitation always belongs to the program of the account that sends it, so it would create a partnership
            //  in the partner's OWN program, not under this parent.
            'can_add_sub_partner'     => $canHaveSubPartners && $canManage,
        ]);
    }

    /**
     * The roles of this partnership (pending ones included, with the status of the assignment), so lists and cards do
     * not need one extra request per row.
     */
    public function includeRoles(Partnerships $model)
    {
        $assignments = PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
            ->where('partnership_id', $model->id)
            ->whereNull('finished_at')
            ->get();

        $roles = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('id', $assignments->pluck('partnership_role_id'))
            ->get()
            ->keyBy('id');

        $rows = $assignments->map(function ($assignment) use ($roles) {
            $role = $roles->get($assignment->partnership_role_id);

            return $role ? [
                'id'                   => $role->uuid,
                'assignment_id'        => $assignment->uuid,
                'name'                 => $role->name,
                'slug'                 => $role->slug,
                'commission_rate'      => $role->commission_rate,
                'parent_override_rate' => $role->parent_override_rate,
                'assignment_status'    => $assignment->status,
            ] : null;
        })->filter()->values();

        return $this->collection($rows, fn($row) => $row);
    }

    /**
     * Branding of the TENANT (logo, colour, name), validated. This is what the invited company sees on the invitation
     * page, the same data the invitation email uses. Visible to everyone who can see the partnership (the tenant and the
     * invited company).
     */
    public function includeBranding(Partnerships $model)
    {
        $tenant = $model->tenant_account_id
            ? IamAccounts::withoutGlobalScope(AuthorizationScope::class)->where('id', $model->tenant_account_id)->first()
            : null;

        return $this->item($model, fn() => $tenant ? PartnershipBranding::forTenant($tenant) : null);
    }

    /**
     * The roles this partner may hand out to sub-partners: the union of allowed_child_role_ids of the roles it holds,
     * only when one of those roles allows sub-partners. The partner side cannot read the tenant's other roles through
     * /partnership-roles (it only sees the roles it holds), so the tenant UI uses this instead.
     */
    public function includeChildRoles(Partnerships $model)
    {
        $held = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('id', PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
                ->where('partnership_id', $model->id)
                ->where('status', 'active')
                ->whereNull('finished_at')
                ->select('partnership_role_id'))
            ->get();

        $childIds = [];
        foreach ($held as $role) {
            if (!empty($role->permissions['can_invite_sub_partners'])) {
                foreach (($role->allowed_child_role_ids ?? []) as $childId) {
                    $childIds[] = (int)$childId;
                }
            }
        }

        $children = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $model->tenant_account_id)
            ->where('status', 'active')
            ->whereIn('id', array_unique($childIds))
            ->get()
            ->map(fn($role) => [
                'id'                   => $role->uuid,
                'name'                 => $role->name,
                'slug'                 => $role->slug,
                'commission_rate'      => $role->commission_rate,
                'parent_override_rate' => $role->parent_override_rate,
            ])->values();

        return $this->collection($children, fn($row) => $row);
    }

    /**
     * Who the partner is and how to reach it: company, owner contact, addresses, tax info and the matching CRM
     * account. Only for the TENANT that owns the partnership; everyone else (the partner looking at its own
     * partnership included) gets null, so the tenant's CRM data never reaches the partner side.
     *
     * The tenant has no permission on the partner's own account rows, so they are read without AuthorizationScope.
     * The CRM row is the tenant's own record of the partner; can_open_crm is checked WITH the scope, so it tells
     * whether this viewer may open that record.
     */
    public function includePartnerProfile(Partnerships $model)
    {
        $isTenant = (int)$model->tenant_account_id === (int)UserHelper::currentAccount()->id;

        if (!$isTenant || !$model->iam_account_id) {
            return $this->null();
        }

        //  A partnership outlives its partner account: a deleted account is still shown, flagged as deleted.
        $account = IamAccounts::withoutGlobalScope(AuthorizationScope::class)
            ->withTrashed()
            ->where('id', $model->iam_account_id)
            ->first();

        if (!$account) {
            return $this->null();
        }

        $owner = $account->iam_user_id
            ? IamUsers::withoutGlobalScope(AuthorizationScope::class)->where('id', $account->iam_user_id)->first()
            : null;
        $country = $account->common_country_id
            ? Countries::withoutGlobalScope(AuthorizationScope::class)->where('id', $account->common_country_id)->first()
            : null;
        $domain = $account->common_domain_id
            ? Domains::withoutGlobalScope(AuthorizationScope::class)->where('id', $account->common_domain_id)->first()
            : null;
        $invitedBy = $model->invited_by_iam_user_id
            ? IamUsers::withoutGlobalScope(AuthorizationScope::class)->where('id', $model->invited_by_iam_user_id)->first()
            : null;

        $accountingAccount = AccountingAccounts::withoutGlobalScope(AuthorizationScope::class)
            ->where($model->accounting_account_id ? 'id' : 'iam_account_id', $model->accounting_account_id ?: $model->iam_account_id)
            ->first();

        $addresses = Addresses::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_type', IamAccounts::class)
            ->where('object_id', $account->id)
            ->orderByDesc('is_invoice_address')
            ->limit(5)
            ->get();

        $countryNames = Countries::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('id', $addresses->pluck('common_country_id')->filter()->unique()->values())
            ->get()
            ->pluck('name', 'id');

        $phoneNumbers = PhoneNumbers::withoutGlobalScope(AuthorizationScope::class)
            ->where('object_type', IamAccounts::class)
            ->where('object_id', $account->id)
            ->limit(5)
            ->get();

        $crm = $this->partnerCrmAccount($model);

        return $this->item($model, fn() => [
            'company' => [
                'name'         => $account->name,
                'description'  => $account->description,
                'phone_number' => $account->phone_number,
                'website'      => $domain?->name,
                'country'      => $country?->name,
                'created_at'   => $account->created_at,
                'is_deleted'   => $account->deleted_at !== null,
            ],
            'owner' => $owner ? [
                'name'         => $owner->fullname ?: trim($owner->name . ' ' . $owner->surname),
                'email'        => $owner->email,
                'phone_number' => $owner->phone_number,
            ] : null,
            'tax' => $accountingAccount ? [
                'tax_number'          => $accountingAccount->tax_number,
                'tax_office'          => $accountingAccount->tax_office,
                'trade_office'        => $accountingAccount->trade_office,
                'trade_office_number' => $accountingAccount->trade_office_number,
            ] : null,
            'addresses' => $addresses->map(fn($address) => [
                'name'               => $address->name,
                'line1'              => $address->line1,
                'line2'              => $address->line2,
                'city'               => $address->city,
                'state'              => $address->state,
                'postcode'           => $address->postcode,
                'country'            => $countryNames->get($address->common_country_id),
                'email_address'      => $address->email_address,
                'is_invoice_address' => (bool)$address->is_invoice_address,
            ])->values()->all(),
            'phone_numbers' => $phoneNumbers->map(fn($phone) => [
                'name'   => $phone->name,
                'number' => trim($phone->code . ' ' . $phone->number),
            ])->values()->all(),
            'office_email'        => $crm['office_email'],
            'office_phone_number' => $crm['office_phone_number'],
            'crm_account_id'      => $crm['id'],
            'can_open_crm'        => $crm['can_open'],
            'invited_by_name'     => $invitedBy ? ($invitedBy->fullname ?: trim($invitedBy->name . ' ' . $invitedBy->surname)) : null,
        ]);
    }

    /**
     * The tenant's CRM record of the partner company. Accounting does not require the CRM package, so everything is
     * null when it is not installed.
     *
     * @return array{id: ?string, can_open: bool, office_email: ?string, office_phone_number: ?string}
     */
    private function partnerCrmAccount(Partnerships $model): array
    {
        $empty = ['id' => null, 'can_open' => false, 'office_email' => null, 'office_phone_number' => null];
        $crmModel = '\\NextDeveloper\\CRM\\Database\\Models\\Accounts';

        if (!class_exists($crmModel)) {
            return $empty;
        }

        $crmAccount = $crmModel::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $model->iam_account_id)
            ->where('owner_iam_account_id', $model->tenant_account_id)
            ->orderBy('id')
            ->first();

        if (!$crmAccount) {
            return $empty;
        }

        return [
            'id'                  => $crmAccount->uuid,
            'can_open'            => $crmModel::where('id', $crmAccount->id)->exists(),
            'office_email'        => $crmAccount->office_email,
            'office_phone_number' => $crmAccount->office_phone_number,
        ];
    }

    /**
     * Live counters (the customer_count column is not maintained by the new customer links).
     */
    public function includeStats(Partnerships $model)
    {
        $customers = DB::table('accounting_account_partners')
            ->where('partnership_id', $model->id)
            ->whereNull('finished_at')
            ->whereNull('deleted_at')
            ->count();

        $subPartners = DB::table('accounting_partnerships')
            ->where('parent_partnership_id', $model->id)
            ->whereIn('status', ['pending', 'active'])
            ->whereNull('deleted_at')
            ->count();

        return $this->item($model, fn() => [
            'customers'    => $customers,
            'sub_partners' => $subPartners,
        ]);
    }

    /**
     * @param Partnerships $model
     *
     * @return array
     */
    public function transform(Partnerships $model)
    {
        $transformed = Cache::get(
            CacheHelper::getKey('Partnerships', $model->uuid, 'Transformed')
        );

        if($transformed) {
            return $transformed;
        }

        $transformed = parent::transform($model);

        Cache::set(
            CacheHelper::getKey('Partnerships', $model->uuid, 'Transformed'),
            $transformed
        );

        return $transformed;
    }
}

<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Helpers\PartnershipHelper;
use NextDeveloper\Accounting\Services\AbstractServices\AbstractPartnershipsService;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;
use Illuminate\Support\Str;
use NextDeveloper\IAM\Helpers\RoleHelper;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Models\Accounts;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\PartnerPrograms;
use NextDeveloper\Accounting\Database\Models\PartnershipRoleAssignments;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\Accounting\Database\Models\AccountPartners;
use NextDeveloper\Accounting\Helpers\AccountingHelper;
use NextDeveloper\Accounting\Helpers\PartnershipTenantGuard;
use NextDeveloper\Accounting\Jobs\SendPartnershipInvitationEmail;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\Commons\Exceptions\NotFoundException;

/**
 * This class is responsible from managing the data for Partnerships
 *
 * Class PartnershipsService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class PartnershipsService extends AbstractPartnershipsService
{

    // EDIT AFTER HERE - WARNING: ABOVE THIS LINE MAY BE REGENERATED AND YOU MAY LOSE CODE

    public static function create(array $data)
    {
        $partnership = null;

        if(array_key_exists('iam_account_id', $data)) {
            $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', $data['iam_account_id'])
                ->first();
        } else {
            $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
                ->where('iam_account_id', UserHelper::currentAccount()->id)
                ->first();
        }

        if($partnership) {
            return $partnership;
        }

        if (!isset($data['partner_code']) || empty($data['partner_code'])) {
            $codeNotValid = true;
            $randomString = '';

            while ($codeNotValid) {
                $randomString = Str::random(10);

                $exists = Partnerships::withoutGlobalScopes()
                    ->where('partner_code', $randomString)
                    ->first();

                if (!$exists)
                    $codeNotValid = false;
            }

            $data['partner_code'] = $randomString;
        }

        $data['customer_count'] = 0;
        $data['level'] = 1;

        $model = parent::create($data);

        return $model;
    }

    private static function addPartnerRoles(Partnerships $partnership)
    {
        $iamAccount = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $partnership->iam_account_id)
            ->first();

        $user = Users::withoutGlobalScope(AuthorizationScope::class)
            ->where('id', $iamAccount->iam_user_id)
            ->first();

        if ($user) {
            RoleHelper::addUserToRole($user, 'accounting-partner');
        }
    }

    public static function approvePartnership(Accounts $account)
    {
        $partnership = PartnershipHelper::getPartnership($account);

        //  Here your partnership is approved email will be sent.

        $partnership->is_approved = true;
        $partnership->save();
    }

    // ---------------------------------------------------------------------------------------------------------
    // Multi-tenant partnership lifecycle: invite -> accept | reject, revoke.
    //
    // The tenant is the account the current user acts as. Authorization is decided EXPLICITLY here (who may do
    // what), and the state writes themselves run with the role check bypassed, because the actor of an accept is
    // the partner account, which has no write permission on the tenant's rows by design.
    // ---------------------------------------------------------------------------------------------------------

    public const STATUS_PENDING = 'pending';
    public const STATUS_ACTIVE = 'active';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_REVOKED = 'revoked';

    /**
     * The tenant (program owner) invites an existing IAM account to be their partner.
     *
     * The partner is created as `pending` and gets no access, commission or customer assignment until the invited
     * company accepts. An invitation that was rejected or revoked earlier can be sent again.
     *
     * @param string      $partnerIamAccountId  uuid of the IAM account (company) that is invited
     * @param array       $roleIds              uuids of the tenant's partnership roles offered to the partner
     * @param string|null $parentPartnershipId  uuid of the parent partnership, when inviting a sub-partner
     *
     * @throws NotAllowedException
     * @throws NotFoundException
     */
    public static function invitePartner(string $partnerIamAccountId, array $roleIds, ?string $parentPartnershipId = null): Partnerships
    {
        $tenant = UserHelper::currentAccount();

        self::assertCanManageProgram();

        //  Only accounts that run a partner program can invite. This also stops an arbitrary account from
        //  turning itself into a tenant.
        $program = PartnerPrograms::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $tenant->id)
            ->where('status', 'active')
            ->first();

        if (!$program) {
            throw new NotAllowedException('Your account does not have an active partner program.');
        }

        if (empty($roleIds)) {
            throw new NotAllowedException('You need to offer at least one partnership role.');
        }

        //  Every invitation sends an email to a third party: limit how many a tenant can send per day, so the feature
        //  cannot be used to spam companies. Counts invitations created in the last 24 hours (re-invitations included).
        $limit = (int)config('accounting.partnership.max_invitations_per_day', 50);

        $sentToday = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $tenant->id)
            ->where('invited_at', '>=', now()->subDay())
            ->count();

        if ($sentToday >= $limit) {
            throw new NotAllowedException('You reached the daily limit of ' . $limit . ' partnership invitations. Try again tomorrow.');
        }

        $partnerIam = Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $partnerIamAccountId)
            ->first();

        if (!$partnerIam) {
            throw new NotFoundException('We cannot find the company you want to invite.');
        }

        if ($partnerIam->id === $tenant->id) {
            throw new NotAllowedException('You cannot invite your own account as a partner.');
        }

        $partnerAccountingAccount = AccountingHelper::getAccountFromIamAccountId($partnerIam->id);

        if (!$partnerAccountingAccount) {
            throw new NotAllowedException('The company you want to invite does not have an accounting account yet.');
        }

        //  Roles: every role must be an active role of OUR tenant.
        $roles = [];
        foreach (array_unique($roleIds) as $roleId) {
            PartnershipTenantGuard::assertOwned(PartnershipRoles::class, $roleId, 'role_ids');

            $role = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)->where('uuid', $roleId)->first();

            if ($role->status !== 'active') {
                throw new NotAllowedException('The role "' . $role->name . '" is not active.');
            }

            $roles[] = $role;
        }

        $parent = self::resolveParentForInvitation($parentPartnershipId, $roles, $program);

        $existing = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $tenant->id)
            ->where('accounting_account_id', $partnerAccountingAccount->id)
            ->first();

        if ($existing && in_array($existing->status, [self::STATUS_PENDING, self::STATUS_ACTIVE])) {
            throw new NotAllowedException('This company is already your partner or has a pending invitation.');
        }

        return DB::transaction(function () use ($tenant, $partnerIam, $partnerAccountingAccount, $roles, $parent, $existing) {
            return UserHelper::withRolesCheckBypassed(function () use ($tenant, $partnerIam, $partnerAccountingAccount, $roles, $parent, $existing) {
                $me = UserHelper::me();

                $data = [
                    'status'                 => self::STATUS_PENDING,
                    'invited_at'             => now(),
                    'accepted_at'            => null,
                    'invited_by_iam_user_id' => $me->id,
                    'parent_partnership_id'  => $parent?->id,
                ];

                if ($existing) {
                    //  Re-invitation of a rejected / revoked partner: reuse the row, keep its partner code.
                    $existing->update($data);
                    $partnership = $existing->fresh();
                } else {
                    $partnership = Partnerships::create($data + [
                        'tenant_account_id'     => $tenant->id,
                        'accounting_account_id' => $partnerAccountingAccount->id,
                        //  In this table iam_account_id is the PARTNER's account (see PartnershipHelper), not the creator.
                        'iam_account_id'        => $partnerIam->id,
                        'iam_user_id'           => $me->id,
                        'partner_code'          => self::generatePartnerCode(),
                        'customer_count'        => 0,
                        'level'                 => 1,
                    ]);
                }

                //  Materialised hierarchy path (ids of the ancestors + self), used for subtree visibility.
                $partnership->path = ($parent && $parent->path ? $parent->path . '.' : '') . $partnership->id;
                $partnership->save();

                foreach ($roles as $role) {
                    //  Starts as pending, becomes active together with the partnership on accept.
                    PartnershipRoleAssignments::create([
                        'tenant_account_id'   => $tenant->id,
                        'partnership_id'      => $partnership->id,
                        'partnership_role_id' => $role->id,
                        'status'              => self::STATUS_PENDING,
                        'started_at'          => now(),
                        'iam_account_id'      => $tenant->id,
                        'iam_user_id'         => $me->id,
                    ]);
                }

                dispatch(new SendPartnershipInvitationEmail($partnership, 'invited'));

                return $partnership->fresh();
            });
        });
    }

    /**
     * The invited company accepts: the partnership and its roles become active and the partner code starts working.
     *
     * @throws NotAllowedException
     * @throws NotFoundException
     */
    public static function acceptInvitation(string $partnershipId): Partnerships
    {
        $partnership = self::getForInvitedAccount($partnershipId);

        UserHelper::withRolesCheckBypassed(function () use ($partnership) {
            DB::transaction(function () use ($partnership) {
                $partnership->update([
                    'status'      => self::STATUS_ACTIVE,
                    'accepted_at' => now(),
                ]);

                PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
                    ->where('partnership_id', $partnership->id)
                    ->where('status', self::STATUS_PENDING)
                    ->whereNull('finished_at')
                    ->get()
                    ->each(fn($assignment) => $assignment->update([
                        'status'     => self::STATUS_ACTIVE,
                        'started_at' => now(),
                    ]));
            });
        });

        dispatch(new SendPartnershipInvitationEmail($partnership->fresh(), 'accepted'));

        return $partnership->fresh();
    }

    /**
     * The invited company rejects the invitation.
     *
     * @throws NotAllowedException
     * @throws NotFoundException
     */
    public static function rejectInvitation(string $partnershipId): Partnerships
    {
        $partnership = self::getForInvitedAccount($partnershipId);

        UserHelper::withRolesCheckBypassed(function () use ($partnership) {
            DB::transaction(function () use ($partnership) {
                $partnership->update(['status' => self::STATUS_REJECTED]);

                self::endRoleAssignments($partnership, self::STATUS_REJECTED);
            });
        });

        dispatch(new SendPartnershipInvitationEmail($partnership->fresh(), 'rejected'));

        return $partnership->fresh();
    }

    /**
     * The tenant ends a partnership (pending or active). Customers stay with the tenant but lose this partner.
     * Sub-partners of the revoked partner are revoked as well, they cannot keep a parent that no longer exists.
     *
     * @throws NotAllowedException
     * @throws NotFoundException
     */
    public static function revokePartner(string $partnershipId): Partnerships
    {
        self::assertCanManageProgram();

        PartnershipTenantGuard::assertOwned(Partnerships::class, $partnershipId, 'partnership_id');

        $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $partnershipId)
            ->first();

        if (!in_array($partnership->status, [self::STATUS_PENDING, self::STATUS_ACTIVE])) {
            throw new NotAllowedException('Only pending or active partnerships can be revoked.');
        }

        UserHelper::withRolesCheckBypassed(function () use ($partnership) {
            DB::transaction(function () use ($partnership) {
                //  The partnership itself and its whole subtree (materialised path).
                $subtree = Partnerships::withoutGlobalScope(AuthorizationScope::class)
                    ->where('tenant_account_id', $partnership->tenant_account_id)
                    ->where(function ($q) use ($partnership) {
                        $q->where('id', $partnership->id)
                            ->orWhere('path', 'like', $partnership->path . '.%');
                    })
                    ->whereIn('status', [self::STATUS_PENDING, self::STATUS_ACTIVE])
                    ->get();

                foreach ($subtree as $node) {
                    $node->update(['status' => self::STATUS_REVOKED]);

                    self::endRoleAssignments($node, self::STATUS_REVOKED);

                    //  The partner no longer serves any customer of the tenant.
                    AccountPartners::withoutGlobalScope(AuthorizationScope::class)
                        ->where('partnership_id', $node->id)
                        ->whereNull('finished_at')
                        ->get()
                        ->each(fn($link) => $link->update(['finished_at' => now()]));
                }
            });
        });

        dispatch(new SendPartnershipInvitationEmail($partnership->fresh(), 'revoked'));

        return $partnership->fresh();
    }

    /**
     * Only people who manage the partner program of the acting account (or platform admins) may invite / revoke.
     */
    private static function assertCanManageProgram(): void
    {
        if (
            UserHelper::hasRole('partnership-manager') ||
            UserHelper::hasRole('accounting-admin') ||
            UserHelper::hasRole('system-admin')
        ) {
            return;
        }

        throw new NotAllowedException('You are not allowed to manage the partner program.');
    }

    /**
     * Finds a PENDING partnership that was addressed to the account the current user acts as, and checks that the
     * user is allowed to answer for that account.
     *
     * @throws NotAllowedException
     * @throws NotFoundException
     */
    private static function getForInvitedAccount(string $partnershipId): Partnerships
    {
        $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $partnershipId)
            ->first();

        //  Same answer for "does not exist" and "is not addressed to you": uuids cannot be probed.
        if (!$partnership || (int)$partnership->iam_account_id !== (int)UserHelper::currentAccount()->id) {
            throw new NotFoundException('We cannot find this invitation.');
        }

        //  Who may answer for the invited company: its account owner, or a partnership manager of that account.
        //  (accounting-admin is deliberately NOT used: it is the cross tenant platform role, normal companies do not
        //  hold it and must never be given it just to accept an invitation.)
        $isOwner = (int)UserHelper::currentAccount()->iam_user_id === (int)UserHelper::me()->id;

        if (!($isOwner || UserHelper::hasRole('partnership-manager') || UserHelper::hasRole('system-admin'))) {
            throw new NotAllowedException('Only the owner or a partnership manager of the invited company can answer an invitation.');
        }

        if ($partnership->status !== self::STATUS_PENDING) {
            throw new NotAllowedException('This invitation is not pending anymore.');
        }

        return $partnership;
    }

    /**
     * Ends the active/pending role assignments of a partnership.
     */
    private static function endRoleAssignments(Partnerships $partnership, string $status): void
    {
        PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
            ->where('partnership_id', $partnership->id)
            ->whereNull('finished_at')
            ->get()
            ->each(fn($assignment) => $assignment->update([
                'status'      => $status,
                'finished_at' => now(),
            ]));
    }

    /**
     * Validates the parent of a sub-partner invitation: it must be an ACTIVE partnership of our tenant, the hierarchy
     * depth limit of the program must hold, and the parent's roles must allow granting the offered roles.
     *
     * @throws NotAllowedException
     */
    private static function resolveParentForInvitation(?string $parentPartnershipId, array $roles, PartnerPrograms $program): ?Partnerships
    {
        if (!$parentPartnershipId) {
            return null;
        }

        PartnershipTenantGuard::assertOwned(Partnerships::class, $parentPartnershipId, 'parent_partnership_id');

        $parent = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('uuid', $parentPartnershipId)
            ->first();

        if ($parent->status !== self::STATUS_ACTIVE) {
            throw new NotAllowedException('The parent partner is not active.');
        }

        //  Depth of the new partner = number of ancestors + parent + itself.
        $depth = $parent->path ? count(explode('.', $parent->path)) + 1 : 2;

        if ($depth > (int)$program->max_hierarchy_depth) {
            throw new NotAllowedException('The partner hierarchy cannot be deeper than ' . $program->max_hierarchy_depth . ' levels.');
        }

        //  Roles the parent is allowed to hand out: union of allowed_child_role_ids of the roles the parent holds,
        //  and the parent must hold at least one role that can invite sub-partners.
        $parentRoles = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('id', PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
                ->where('partnership_id', $parent->id)
                ->where('status', self::STATUS_ACTIVE)
                ->whereNull('finished_at')
                ->select('partnership_role_id'))
            ->get();

        $allowedChildIds = [];
        $canInvite = false;

        foreach ($parentRoles as $parentRole) {
            if (!empty($parentRole->permissions['can_invite_sub_partners'])) {
                $canInvite = true;
            }

            foreach (($parentRole->allowed_child_role_ids ?? []) as $childId) {
                $allowedChildIds[] = (int)$childId;
            }
        }

        if (!$canInvite) {
            throw new NotAllowedException('The parent partner is not allowed to have sub-partners.');
        }

        foreach ($roles as $role) {
            if (!in_array((int)$role->id, $allowedChildIds, true)) {
                throw new NotAllowedException('The parent partner cannot hand out the role "' . $role->name . '".');
            }
        }

        return $parent;
    }

    /**
     * Partner codes are globally unique (the tenant is derived from the code at signup).
     */
    private static function generatePartnerCode(): string
    {
        do {
            $code = Str::random(10);

            $exists = Partnerships::withoutGlobalScopes()->where('partner_code', $code)->exists();
        } while ($exists);

        return $code;
    }
}

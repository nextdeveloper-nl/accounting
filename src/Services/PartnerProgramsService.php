<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Services\AbstractServices\AbstractPartnerProgramsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\IAM\Database\Models\Accounts as IamAccounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Database\Models\PartnerPrograms;
use NextDeveloper\Commons\Exceptions\NotAllowedException;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\RoleHelper;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * This class is responsible from managing the data for PartnerPrograms
 *
 * Class PartnerProgramsService.
 *
 * @package NextDeveloper\Accounting\Database\Models
 */
class PartnerProgramsService extends AbstractPartnerProgramsService
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
        //  The program status is controlled by enableProgram()/disableProgram() and by the platform (suspend). A tenant
        //  must not set it through the generic endpoint, otherwise it could lift a suspension.
        if (!self::canSetStatus()) {
            unset($data['status']);
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

        //  Same rule on update (a tenant cannot un-suspend its own program).
        if (!self::canSetStatus()) {
            unset($data['status']);
        }

        return parent::update($id, $data);
    }

    // ---------------------------------------------------------------------------------------------------------
    // Tenant onboarding: any account OWNER can turn their account into a partner program (a tenant).
    // ---------------------------------------------------------------------------------------------------------

    public const STATUS_ACTIVE = 'active';
    public const STATUS_DISABLED = 'disabled';   //  turned off by the owner, can be turned on again by the owner
    public const STATUS_SUSPENDED = 'suspended'; //  turned off by the platform, the owner cannot undo it

    /**
     * Turns the account the current user acts as into a partner program and makes the owner its partnership manager.
     *
     * Only the OWNER of the account may do this (self service, no platform staff needed). It is idempotent: calling it
     * again on an active program changes nothing, calling it on a disabled one turns it back on. A program that was
     * suspended by the platform cannot be re-enabled by its owner.
     *
     * The program starts with NO partnership roles, no partners and neutral branding: the tenant defines its own roles
     * (and their commission rates) afterwards. Nothing is copied from the PlusClouds program on purpose, those are
     * commercial terms of that program.
     *
     * @throws NotAllowedException
     */
    public static function enableProgram(): PartnerPrograms
    {
        $account = UserHelper::currentAccount();
        $me = UserHelper::me();

        self::assertIsAccountOwner($account, $me);

        $program = PartnerPrograms::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $account->id)
            ->first();

        if ($program && $program->status === self::STATUS_SUSPENDED) {
            throw new NotAllowedException('Your partner program was suspended. Please contact support.');
        }

        return DB::transaction(function () use ($account, $me, $program) {
            //  The owner has no partnership role yet (they are about to get it), so the role check is bypassed for
            //  these writes. Authorization was decided above: only the account owner gets here.
            $program = UserHelper::withRolesCheckBypassed(function () use ($account, $me, $program) {
                if ($program) {
                    if ($program->status !== self::STATUS_ACTIVE) {
                        $program->update(['status' => self::STATUS_ACTIVE]);
                    }

                    return $program->fresh();
                }

                return PartnerPrograms::create([
                    'tenant_account_id'    => $account->id,
                    'responsible_email'    => $me->email,
                    //  Neutral branding: the tenant name, no logo, default colour (emails fall back to it).
                    'branding'             => ['display_name' => $account->name],
                    'max_hierarchy_depth'  => 3,
                    'status'               => self::STATUS_ACTIVE,
                    'iam_account_id'       => $account->id,
                    'iam_user_id'          => $me->id,
                ]);
            });

            //  The owner manages the program. Idempotent. A missing role row (leo:generate-roles not run) must fail
            //  loudly and roll the program back, otherwise the owner would own a program nobody can manage.
            if (!RoleHelper::addUserToRole($me, 'partnership-manager')) {
                throw new \RuntimeException(
                    'The partnership-manager role is not available. Run leo:generate-roles and try again.'
                );
            }

            return $program->fresh();
        });
    }

    /**
     * Turns the program off: no new invitations can be sent. Existing partners, customers and commissions are not
     * touched. The owner (or a platform admin) can turn it on again with enableProgram().
     *
     * @throws NotAllowedException
     */
    public static function disableProgram(): PartnerPrograms
    {
        $account = UserHelper::currentAccount();
        $me = UserHelper::me();

        if (!UserHelper::hasRole('system-admin')) {
            self::assertIsAccountOwner($account, $me);
        }

        $program = PartnerPrograms::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $account->id)
            ->first();

        if (!$program) {
            throw new NotAllowedException('Your account does not have a partner program.');
        }

        if ($program->status === self::STATUS_SUSPENDED) {
            //  Suspended is a platform decision, it must not be downgraded to disabled (which the owner could undo).
            throw new NotAllowedException('Your partner program was suspended. Please contact support.');
        }

        UserHelper::withRolesCheckBypassed(function () use ($program) {
            $program->update(['status' => self::STATUS_DISABLED]);
        });

        return $program->fresh();
    }

    /**
     * Only system-admin may write the program status directly (this is how the platform suspends / un-suspends).
     *
     * accounting-admin is deliberately NOT enough: roles are assigned per account, so a tenant's own admin could hold
     * it in their account and lift a suspension of their own program. system-admin cannot be handed out through
     * normal role assignment (see RolesService::NON_ASSIGNABLE_ROLES).
     */
    private static function canSetStatus(): bool
    {
        return UserHelper::hasRole('system-admin');
    }

    /**
     * Discovery: the partner programs that take applications, for any logged in account that wants to apply.
     *
     * This is a directory of OTHER companies' programs, so it is deliberately narrow: only programs that are active and
     * opted in (accepting_applications), the account's own program is left out, and a program shows only what an
     * applicant needs: the VERIFIED account name (never the tenant typed branding, so a program cannot pose as another
     * company), its active roles (id and name, no commission rates or other terms) and the status of the caller's own
     * partnership with it. No emails, no owners. Prefix search on the name, paged, and limited per account per hour.
     *
     * @param  string|null $query   prefix of the company name
     * @param  int         $page
     * @param  int         $perPage at most 50
     * @return array {data: [{id, name, roles: [{id, name}], my_status}], meta: {page, per_page, total}}
     * @throws NotAllowedException
     */
    public static function discoverPrograms(?string $query, int $page = 1, int $perPage = 20): array
    {
        $account = UserHelper::currentAccount();

        $limit = (int)config('accounting.partnership.max_discovery_requests_per_hour', 120);

        if (RateLimiter::hit('partnership-discover:account:' . $account->id, 3600) > $limit) {
            throw new NotAllowedException('You searched too often. Please try again later.');
        }

        $page = max(1, $page);
        $perPage = min(50, max(1, $perPage));

        //  Until the accepting_applications column exists nothing can be listed (checked at most every 10 minutes).
        $hasColumn = Cache::remember('partnership:programs-has-accepting-applications', 600, function () {
            return Schema::hasColumn('accounting_partner_programs', 'accepting_applications');
        });

        if (!$hasColumn) {
            return ['data' => [], 'meta' => ['page' => $page, 'per_page' => $perPage, 'total' => 0]];
        }

        $programs = PartnerPrograms::withoutGlobalScope(AuthorizationScope::class)
            //  Columns are qualified: the query joins iam_accounts.
            ->where('accounting_partner_programs.status', self::STATUS_ACTIVE)
            ->where('accounting_partner_programs.accepting_applications', true)
            ->where('accounting_partner_programs.tenant_account_id', '!=', $account->id)
            ->join('iam_accounts', 'iam_accounts.id', '=', 'accounting_partner_programs.tenant_account_id')
            ->whereNull('iam_accounts.deleted_at')
            ->select('accounting_partner_programs.tenant_account_id', 'iam_accounts.uuid as account_uuid', 'iam_accounts.name as account_name');

        $query = trim((string)$query);

        if ($query !== '') {
            //  Escape LIKE wildcards: the text is a literal prefix.
            $prefix = str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], mb_substr($query, 0, 100)) . '%';
            $programs->where('iam_accounts.name', 'ilike', $prefix);
        }

        $total = (clone $programs)->count();

        $rows = $programs->orderBy('iam_accounts.name')->forPage($page, $perPage)->get();

        $tenantIds = $rows->pluck('tenant_account_id')->all();

        //  Active roles per program, in one query.
        $roles = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('tenant_account_id', $tenantIds)
            ->where('status', 'active')
            ->orderBy('priority', 'desc')
            ->get(['uuid', 'name', 'tenant_account_id'])
            ->groupBy('tenant_account_id');

        //  The caller's own partnership with each program (any status), in one query.
        $mine = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('tenant_account_id', $tenantIds)
            ->where('iam_account_id', $account->id)
            ->get(['tenant_account_id', 'status'])
            ->pluck('status', 'tenant_account_id');

        $data = $rows->map(fn($row) => [
            'id'        => $row->account_uuid,
            'name'      => $row->account_name,
            'roles'     => ($roles->get($row->tenant_account_id) ?? collect())
                ->map(fn($r) => ['id' => $r->uuid, 'name' => $r->name])->values()->all(),
            'my_status' => $mine->get($row->tenant_account_id),
        ])->values()->all();

        return ['data' => $data, 'meta' => ['page' => $page, 'per_page' => $perPage, 'total' => $total]];
    }

    /**
     * One call that tells the UI everything it needs to draw the navigation and the first screen:
     * is this account a tenant, may the user enable the program, how many invitations wait, how many partnerships are
     * active, and how many invitation emails the tenant may still send today.
     *
     * Everything is read for the account the user acts as, with explicit conditions (no role scope involved).
     */
    public static function getSummary(): array
    {
        $account = UserHelper::currentAccount();
        $me = UserHelper::me();

        $program = PartnerPrograms::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $account->id)
            ->first();

        $isOwner = (int)$account->iam_user_id === (int)$me->id;
        $isManager = UserHelper::hasRole('partnership-manager');

        $asPartner = Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('iam_account_id', $account->id);

        $perTenant = (int)config('accounting.partnership.max_invitations_per_day', 50);
        $perPartner = (int)config('accounting.partnership.max_emails_per_partner_per_day', 3);

        return [
            'is_tenant'                => $program !== null && $program->status === self::STATUS_ACTIVE,
            'program'                  => $program ? [
                'id'                  => $program->uuid,
                'status'              => $program->status,
                'branding'            => $program->branding,
                'responsible_email'   => $program->responsible_email,
                'max_hierarchy_depth' => $program->max_hierarchy_depth,
                //  Whether other companies may apply (null until the accepting_applications column exists).
                'accepting_applications' => (bool)$program->accepting_applications,
            ] : null,
            //  Only the owner can enable, and not a program the platform suspended.
            'can_enable'               => $isOwner && (!$program || $program->status === self::STATUS_DISABLED),
            'can_manage'               => $isManager,
            'pending_invitations'      => (clone $asPartner)->where('status', 'pending')->count(),
            'active_partnerships'      => (clone $asPartner)->where('status', 'active')->count(),
            'invitation_quota'         => $program ? [
                'per_day_remaining'      => RateLimiter::remaining('partnership-email:tenant:' . $account->id, $perTenant),
                'per_day_limit'          => $perTenant,
                'per_partner_per_day_limit' => $perPartner,
            ] : null,
        ];
    }

    /**
     * @throws NotAllowedException
     */
    private static function assertIsAccountOwner($account, $me): void
    {
        if ((int)$account->iam_user_id !== (int)$me->id) {
            throw new NotAllowedException('Only the owner of the account can manage its partner program settings.');
        }
    }
}

<?php

namespace NextDeveloper\Accounting\Services;

use NextDeveloper\Accounting\Services\AbstractServices\AbstractPartnerProgramsService;
use Illuminate\Support\Facades\DB;
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
     * Only platform staff may write the program status directly.
     */
    private static function canSetStatus(): bool
    {
        return UserHelper::hasRole('system-admin') || UserHelper::hasRole('accounting-admin');
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

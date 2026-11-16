<?php

namespace NextDeveloper\Accounting\Http\Transformers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use NextDeveloper\Accounting\Database\Models\PartnershipRoleAssignments;
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
            'can_add_sub_partner'     => $canHaveSubPartners && ($canManage || ($isPartner && ($isOwner || $isManager))),
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

<?php

namespace NextDeveloper\Accounting\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use NextDeveloper\Accounting\Database\Models\PartnerPrograms;
use NextDeveloper\Accounting\Database\Models\PartnershipRoleAssignments;
use NextDeveloper\Accounting\Database\Models\PartnershipRoles;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\Accounting\Envelopes\Partnerships\PartnershipInvitationEnvelope;
use NextDeveloper\Communication\Helpers\Communicate;
use NextDeveloper\IAM\Database\Models\Accounts as IamAccounts;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;
use NextDeveloper\IAM\Helpers\UserHelper;

/**
 * Sends the partnership lifecycle email (invited, accepted, rejected, revoked), branded with the tenant's logo.
 *
 * Dispatched directly from PartnershipsService (no event handler). Only the partnership ID is serialised and the
 * model is reloaded without AuthorizationScope, because a queue worker has no logged in user.
 */
class SendPartnershipInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    private int $partnershipId;

    /**
     * @param Partnerships $partnership
     * @param string       $event       invited | accepted | rejected | revoked
     */
    public function __construct(Partnerships $partnership, private string $event)
    {
        $this->partnershipId = $partnership->id;
        $this->queue = 'accounting';
    }

    public function handle(): void
    {
        $partnership = Partnerships::withoutGlobalScope(AuthorizationScope::class)->find($this->partnershipId);

        if (!$partnership) {
            Log::warning('[SendPartnershipInvitationEmail] Partnership not found: ' . $this->partnershipId);
            return;
        }

        $tenant = IamAccounts::withoutGlobalScope(AuthorizationScope::class)->find($partnership->tenant_account_id);
        $partner = IamAccounts::withoutGlobalScope(AuthorizationScope::class)->find($partnership->iam_account_id);

        if (!$tenant || !$partner) {
            Log::warning('[SendPartnershipInvitationEmail] Tenant or partner account not found for partnership: '
                . $partnership->uuid);
            return;
        }

        $branding = $this->getBranding($tenant);
        $details = $this->getDetails($partnership, $tenant, $partner);
        $fromName = $this->getFromName($tenant);
        $replyTo = $this->getReplyTo($tenant);

        foreach ($this->getRecipients($tenant, $partner) as $user) {
            try {
                (new Communicate($user))->sendEnvelope(
                    new PartnershipInvitationEnvelope($this->event, $branding, $details, $user, $fromName, $replyTo)
                );
            } catch (\Throwable $e) {
                //  One broken mailbox must not stop the others.
                Log::error('[SendPartnershipInvitationEmail] Cannot send to user ' . $user->uuid . ': ' . $e->getMessage());
            }
        }
    }

    /**
     * invited / revoked   -> the invited company: its account owner and partnership managers
     * accepted / rejected -> the tenant: its account owner and partnership managers
     *
     * (accounting-admin is not used on purpose, it is the cross tenant platform role and normal accounts do not
     * hold it.)
     */
    private function getRecipients(IamAccounts $tenant, IamAccounts $partner)
    {
        $account = in_array($this->event, ['invited', 'revoked']) ? $partner : $tenant;

        $users = UserHelper::getUsersWithRole('partnership-manager', account: $account, authorization: true);

        $owner = UserHelper::getAccountOwner($account);

        if ($owner) {
            $users = $users->push($owner);
        }

        //  The owner can also be a partnership manager: one email per person.
        return $users->unique('id')->values();
    }

    /**
     * Sender display name: always "<verified account name> via <platform>". The account name comes from IAM, not from
     * the tenant branding, and is stripped of everything that could turn it into an address or a header injection, so a
     * tenant cannot pose as the platform or as another company in the From line.
     */
    private function getFromName(IamAccounts $tenant): string
    {
        $platform = (string)config('leo.mail.from_name');

        //  Control characters (header injection), angle brackets, quotes and @ (fake addresses) are removed.
        $name = preg_replace('/[\x00-\x1F\x7F<>"@]/u', '', (string)$tenant->name);
        $name = mb_substr(trim($name), 0, 60);

        //  An account named like the platform would read as "<platform> via <platform>": use a neutral label.
        if ($name === '' || ($platform !== '' && stripos($name, $platform) !== false)) {
            $name = 'A partner program';
        }

        return $platform !== '' ? $name . ' via ' . $platform : $name;
    }

    /**
     * Reply-To is the email of the tenant's account owner (a verified IAM user), never the tenant typed support
     * address, which nobody verified. The support address is only shown as text in the body.
     */
    private function getReplyTo(IamAccounts $tenant): ?string
    {
        $owner = UserHelper::getAccountOwner($tenant);

        $email = $owner?->email;

        return ($email && filter_var($email, FILTER_VALIDATE_EMAIL)) ? $email : null;
    }

    /**
     * Branding of the tenant's program. The values are controlled by the tenant, so they are validated before they
     * end up in an email: https logo only, hex colour only, valid email only.
     */
    private function getBranding(IamAccounts $tenant): array
    {
        $program = PartnerPrograms::withoutGlobalScope(AuthorizationScope::class)
            ->where('tenant_account_id', $tenant->id)
            ->first();

        $raw = (array)($program?->branding ?? []);

        $logo = $raw['logo_url'] ?? null;
        if (!is_string($logo) || !filter_var($logo, FILTER_VALIDATE_URL) || !str_starts_with(strtolower($logo), 'https://')) {
            $logo = null;
        }

        $color = $raw['primary_color'] ?? null;
        if (!is_string($color) || !preg_match('/^#[0-9a-fA-F]{3,8}$/', $color)) {
            $color = '#2563eb';
        }

        $support = $raw['support_email'] ?? $program?->responsible_email;
        if (!is_string($support) || !filter_var($support, FILTER_VALIDATE_EMAIL)) {
            $support = null;
        }

        $name = $raw['display_name'] ?? null;
        if (!is_string($name) || trim($name) === '') {
            $name = $tenant->name;
        }

        return [
            'display_name'  => mb_substr($name, 0, 100),
            'logo_url'      => $logo,
            'primary_color' => $color,
            'support_email' => $support,
        ];
    }

    private function getDetails(Partnerships $partnership, IamAccounts $tenant, IamAccounts $partner): array
    {
        $roleNames = PartnershipRoles::withoutGlobalScope(AuthorizationScope::class)
            ->whereIn('id', PartnershipRoleAssignments::withoutGlobalScope(AuthorizationScope::class)
                ->where('partnership_id', $partnership->id)
                ->select('partnership_role_id'))
            ->pluck('name')
            ->all();

        $parentName = null;
        if ($partnership->parent_partnership_id) {
            $parent = Partnerships::withoutGlobalScope(AuthorizationScope::class)->find($partnership->parent_partnership_id);
            $parentName = $parent
                ? IamAccounts::withoutGlobalScope(AuthorizationScope::class)->find($parent->iam_account_id)?->name
                : null;
        }

        //  The panel page where the invited company reviews the invitation (accepting is an authenticated API call).
        $base = config('accounting.partnership.panel_url') ?: config('app.url');
        $actionUrl = $base ? rtrim($base, '/') . '/partnership/invitations/' . $partnership->uuid : null;

        return [
            'tenant_name' => $tenant->name,
            'partner_name' => $partner->name,
            'parent_name' => $parentName,
            'roles'       => $roleNames,
            'action_url'  => $actionUrl,
        ];
    }
}

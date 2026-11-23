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
use NextDeveloper\Accounting\Helpers\PartnershipBranding;
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
     * @param string       $event       invited | accepted | rejected | revoked | applied | approved | declined
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
     * applied             -> the tenant (someone asks to join its program)
     * approved / declined -> the applicant company (the tenant answered its application)
     *
     * (accounting-admin is not used on purpose, it is the cross tenant platform role and normal accounts do not
     * hold it.)
     */
    private function getRecipients(IamAccounts $tenant, IamAccounts $partner)
    {
        $account = in_array($this->event, ['invited', 'revoked', 'approved', 'declined']) ? $partner : $tenant;

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
     * the tenant branding, and is reduced to plain visible text so a tenant cannot pose as the platform or as another
     * company in the From line.
     */
    private function getFromName(IamAccounts $tenant): string
    {
        $platform = (string)config('leo.mail.from_name');

        $name = self::plainText((string)$tenant->name);
        $name = mb_substr($name, 0, 60);

        //  A name that reads like the platform (also with look-alike letters, spacing or punctuation tricks) would make
        //  the sender look like the platform itself: use a neutral label.
        if ($name === '' || ($platform !== '' && self::looksLikePlatform($name, $platform))) {
            $name = 'A partner program';
        }

        return $platform !== '' ? $name . ' via ' . $platform : $name;
    }

    /**
     * Plain visible text: NFKC normalised, every Unicode control / format / private-use / separator character removed
     * (this covers zero-width characters U+200B-U+200F, bidi overrides U+202A-U+202E and isolates U+2066-U+2069 and
     * CR/LF header injection), and the characters that can fake an address or markup (< > " @) removed.
     */
    private static function plainText(string $value): string
    {
        if (class_exists(\Normalizer::class)) {
            $normalized = \Normalizer::normalize($value, \Normalizer::FORM_KC);
            $value = $normalized === false ? $value : $normalized;
        }

        $value = preg_replace('/[\p{C}\p{Zl}\p{Zp}<>"@]/u', '', $value) ?? '';

        //  Collapse the remaining (visible) whitespace
        return trim(preg_replace('/\s+/u', ' ', $value) ?? '');
    }

    /**
     * True when the name contains something that reads like the platform name to a person: the same name after
     * transliteration / digit swaps (skeleton), or a look-alike spelled with confusable letters from other scripts
     * (e.g. Cyrillic "Р" for "P"), which ICU's Spoofchecker detects.
     */
    private static function looksLikePlatform(string $name, string $platform): bool
    {
        if (str_contains(self::skeleton($name), self::skeleton($platform))) {
            return true;
        }

        if (!class_exists(\Spoofchecker::class)) {
            return false;
        }

        //  Letters and digits only, lower case, so that spacing / punctuation tricks do not hide the match.
        $letters = fn(string $v) => mb_strtolower(preg_replace('/[^\p{L}\p{N}]/u', '', self::plainText($v)) ?? '');

        $needle = $letters($platform);
        $haystack = $letters($name);
        $length = mb_strlen($needle);

        if ($length === 0 || mb_strlen($haystack) < $length) {
            return false;
        }

        $checker = new \Spoofchecker();

        //  Slide a window of the platform name's length over the name. Names are cut to 60 chars, so this is cheap.
        for ($i = 0; $i <= mb_strlen($haystack) - $length; $i++) {
            if ($checker->areConfusable(mb_substr($haystack, $i, $length), $needle)) {
                return true;
            }
        }

        return false;
    }

    /**
     * "Skeleton" used to compare names: transliterated to ASCII (so Cyrillic / Greek look-alikes of Latin letters map to
     * Latin), lower case, letters and digits only. Two names that look the same to a person have the same skeleton.
     */
    private static function skeleton(string $value): string
    {
        $value = self::plainText($value);

        if (class_exists(\Transliterator::class)) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII; Lower()');

            if ($transliterator) {
                $value = $transliterator->transliterate($value) ?: $value;
            }
        }

        $value = mb_strtolower($value);

        //  Common digit/letter swaps used to dodge a name check (pl0uscl0uds)
        $value = strtr($value, ['0' => 'o', '1' => 'l', '3' => 'e', '4' => 'a', '5' => 's', '7' => 't', '$' => 's']);

        return preg_replace('/[^a-z]/', '', $value) ?? '';
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
     * Branding of the tenant's program, validated (https logo, hex colour, valid email). See PartnershipBranding.
     */
    private function getBranding(IamAccounts $tenant): array
    {
        return PartnershipBranding::forTenant($tenant);
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

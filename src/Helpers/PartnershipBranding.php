<?php

namespace NextDeveloper\Accounting\Helpers;

use NextDeveloper\Accounting\Database\Models\PartnerPrograms;
use NextDeveloper\IAM\Database\Models\Accounts as IamAccounts;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

/**
 * Branding of a tenant (partner program owner), as it is shown to OTHER people: in invitation emails and on the
 * invitation page of the invited company.
 *
 * The values are typed in by the tenant, so they are validated before anybody else sees them: the logo must be an
 * https URL, the colour a hex value and the support email a valid address. Anything else falls back to a safe default.
 * Values are still HTML-escaped where they are rendered.
 */
class PartnershipBranding
{
    public const DEFAULT_COLOR = '#2563eb';

    /**
     * @param IamAccounts $tenant the IAM account that owns the program
     * @return array display_name, logo_url (https or null), primary_color, support_email (or null)
     */
    public static function forTenant(IamAccounts $tenant): array
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
            $color = self::DEFAULT_COLOR;
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
}

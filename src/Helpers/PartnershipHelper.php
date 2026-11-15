<?php

namespace NextDeveloper\Accounting\Helpers;

use Illuminate\Support\Str;
use NextDeveloper\Accounting\Database\Models\Accounts;
use NextDeveloper\Accounting\Database\Models\Partnerships;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Database\Scopes\AuthorizationScope;

class PartnershipHelper
{
    public static function getPartnerByCode($partnerCode) : ?Accounts
    {
        if(!$partnerCode) return null;

        return Accounts::withoutGlobalScope(AuthorizationScope::class)
            ->where('partner_code', $partnerCode)
            ->first();
    }

    /**
     * Returns the partnership of an account.
     *
     * Multi-tenant: an account can be a partner in several tenants (programs). Pass $tenantAccountId (IAM account id of
     * the program owner) to get the partnership in that tenant. Without it the first partnership is returned, which
     * is the previous single tenant behaviour and is only unambiguous while the account has one partnership.
     */
    public static function getPartnership(Accounts|\NextDeveloper\IAM\Database\Models\Accounts $account, ?int $tenantAccountId = null)
    {
        $iamAccountId = match (get_class($account)) {
            Accounts::class => $account->iam_account_id,
            \NextDeveloper\IAM\Database\Models\Accounts::class => $account->id,
            default => null,
        };

        if ($iamAccountId === null) {
            return null;
        }

        $query = Partnerships::where('iam_account_id', $iamAccountId);

        if ($tenantAccountId !== null) {
            $query->where('tenant_account_id', $tenantAccountId);
        }

        return $query->first();
    }

    /**
     * Multi-tenant partner code lookup. Partner codes are globally unique, so the code alone identifies the
     * partnership AND its tenant ($partnership->tenant_account_id).
     *
     * Only ACTIVE partnerships resolve: a pending, rejected or revoked partner must not be able to attract customers.
     * The lookup bypasses AuthorizationScope on purpose, a person signing up has no role yet.
     */
    public static function getActivePartnershipByCode(?string $partnerCode): ?Partnerships
    {
        if (!$partnerCode) {
            return null;
        }

        return Partnerships::withoutGlobalScope(AuthorizationScope::class)
            ->where('partner_code', $partnerCode)
            ->where('status', 'active')
            ->whereNotNull('tenant_account_id')
            ->first();
    }

    public static function getPartnerAccountOwner(Accounts|string $account)
    {
        if(is_string($account)) {
            if(Str::isUuid($account)) {
                $account = Accounts::withoutGlobalScopes()
                    ->where('uuid', $account)
                    ->first();
            } else {
                $account = Accounts::withoutGlobalScopes()
                    ->where('id', $account)
                    ->first();
            }
        }

        $account = \NextDeveloper\IAM\Database\Models\Accounts::withoutGlobalScopes()
            ->where('id', $account->iam_account_id)
            ->first();

        return Users::withoutGlobalScopes()
            ->where('id', $account->iam_user_id)
            ->first();
    }
}

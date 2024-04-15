<?php

namespace NextDeveloper\Accounting\Authorization\Roles;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use NextDeveloper\CRM\Database\Models\AccountManagers;
use NextDeveloper\IAM\Authorization\Roles\AbstractRole;
use NextDeveloper\IAM\Authorization\Roles\IAuthorizationRole;
use NextDeveloper\IAM\Database\Models\Users;
use NextDeveloper\IAM\Helpers\UserHelper;

class AccountingUserRole extends AbstractRole implements IAuthorizationRole
{
    public const NAME = 'agenda-admin';

    public const LEVEL = 100;

    public const DESCRIPTION = 'Agenda Admin';

    public const DB_PREFIX = 'agenda';

    /**
     * Applies basic member role sql for Eloquent
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where([
            'iam_account_id'    =>  UserHelper::currentAccount()->id,
            'iam_user_id'       =>  UserHelper::me()->id
        ]);
    }

    public function checkPrivileges(Users $users = null)
    {
        //return UserHelper::hasRole(self::NAME, $users);
    }

    public function getModule()
    {
        return 'agenda';
    }

    public function allowedOperations() :array
    {
        return [
            'agenda_calendars:read',
            'agenda_calendars:create',
            'agenda_calendars:update',
            'agenda_calendars:delete',
            'agenda_calendar_items:read',
            'agenda_calendar_items:create',
            'agenda_calendar_items:update',
            'agenda_calendar_items:delete',
            'agenda_calendar_subscriptions:read',
            'agenda_calendar_subscriptions:create',
            'agenda_calendar_subscriptions:update',
            'agenda_calendar_subscriptions:delete',
            'agenda_contacts:read',
            'agenda_contacts:create',
            'agenda_contacts:update',
            'agenda_contacts:delete',
            'agenda_address_books:read',
            'agenda_address_books:create',
            'agenda_address_books:update',
            'agenda_address_books:delete',
        ];
    }

    public function getLevel(): int
    {
        return self::LEVEL;
    }

    public function getDescription(): string
    {
        return self::DESCRIPTION;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function canBeApplied($column)
    {
        if(self::DB_PREFIX === '*') {
            return true;
        }

        if(Str::startsWith($column, self::DB_PREFIX)) {
            return true;
        }

        return false;
    }

    public function getDbPrefix()
    {
        return self::DB_PREFIX;
    }

    public function checkRules(Users $users): bool
    {
        // TODO: Implement checkRules() method.
    }
}

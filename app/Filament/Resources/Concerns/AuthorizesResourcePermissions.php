<?php

namespace App\Filament\Resources\Concerns;

trait AuthorizesResourcePermissions
{
    public static function canViewAny(): bool
    {
        return auth()->user()?->can(static::$permissionPrefix.'.view') ?? false;
    }

    public static function canView($record): bool
    {
        return static::canViewAny();
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->can(static::$permissionPrefix.'.create') ?? false;
    }

    public static function canEdit($record): bool
    {
        return auth()->user()?->can(static::$permissionPrefix.'.update') ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->can(static::$permissionPrefix.'.delete') ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->can(static::$permissionPrefix.'.delete') ?? false;
    }
}

<?php

namespace App\Filament\Pages\Concerns;

trait AuthorizesPagePermission
{
    public static function canAccess(): bool
    {
        return auth()->user()?->can(static::$requiredPermission) ?? false;
    }
}

<?php

namespace App\Filament\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Доступ до ресурсу адмінки лише для перелічених ролей.
 * Клас оголошує: protected static array $roles = ['admin', ...];
 */
trait RestrictedToRoles
{
    public static function can(string $action, ?Model $record = null): bool
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, static::$roles, true)) {
            return false;
        }

        return parent::can($action, $record);
    }
}

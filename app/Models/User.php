<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'locale', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Ролі персоналу — мають доступ до адмінки. */
    public const STAFF_ROLES = ['admin', 'manager', 'content'];

    /** Ролі покупців (кабінети — фаза 4). */
    public const CUSTOMER_ROLES = ['client', 'business_client', 'business_partner'];

    public function isStaff(): bool
    {
        return in_array($this->role, self::STAFF_ROLES, true);
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->isStaff();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}

<?php

namespace App\Models;

use App\Notifications\BusinessRoleDecision;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name', 'last_name', 'email', 'phone', 'company', 'company_code', 'password', 'locale', 'role',
    'requested_role', 'role_requested_at', 'default_shipping_provider_id', 'default_payment_provider_id',
    'default_city', 'default_address', 'notifications', 'last_login_at',
])]
#[Hidden(['password', 'remember_token', 'email_code'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /** Ролі персоналу — мають доступ до адмінки. */
    public const STAFF_ROLES = ['admin', 'manager', 'content'];

    /** Ролі покупців. */
    public const CUSTOMER_ROLES = ['client', 'business_client', 'business_partner'];

    /** Бізнес-ролі: надаються лише після підтвердження заявки менеджером. */
    public const BUSINESS_ROLES = ['business_client', 'business_partner'];

    /** Сповіщення за замовчуванням (кабінет → Налаштування). */
    public const DEFAULT_NOTIFICATIONS = ['email_orders' => true, 'sms_orders' => true, 'newsletter' => false];

    public function isBusiness(): bool
    {
        return in_array($this->role, self::BUSINESS_ROLES, true);
    }

    public function fullName(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    public function notificationSettings(): array
    {
        return array_replace(self::DEFAULT_NOTIFICATIONS, $this->notifications ?? []);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /** Менеджер підтвердив заявку на бізнес-роль. */
    public function approveBusinessRole(): void
    {
        if (! in_array($this->requested_role, self::BUSINESS_ROLES, true)) {
            return;
        }
        $role = $this->requested_role;
        $this->forceFill(['role' => $role, 'requested_role' => null, 'role_requested_at' => null])->save();
        $this->notify(new BusinessRoleDecision($role, true));
    }

    public function rejectBusinessRole(): void
    {
        if (! $this->requested_role) {
            return;
        }
        $role = $this->requested_role;
        $this->forceFill(['requested_role' => null, 'role_requested_at' => null])->save();
        $this->notify(new BusinessRoleDecision($role, false));
    }

    public function documents()
    {
        return $this->hasMany(CustomerDocument::class);
    }

    public function cashbackTransactions()
    {
        return $this->hasMany(CashbackTransaction::class);
    }

    public function supportRequests()
    {
        return $this->hasMany(Lead::class)->where('type', 'support');
    }

    /** Нормалізований телефон: лише цифри з +380… (для входу за телефоном і унікальності). */
    public static function normalizePhone(?string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if ($digits === '') {
            return null;
        }
        if (strlen($digits) === 10 && str_starts_with($digits, '0')) {
            $digits = '38' . $digits;
        }

        return '+' . $digits;
    }

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
            'email_verified_at'  => 'datetime',
            'password'           => 'hashed',
            'notifications'      => 'array',
            'role_requested_at'  => 'datetime',
            'email_code_sent_at' => 'datetime',
            'last_login_at'      => 'datetime',
        ];
    }
}

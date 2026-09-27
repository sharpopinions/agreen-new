<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class PaymentProvider extends Model
{
    use BelongsToStore, HasTranslatedName;

    protected $fillable = ['store_id', 'driver', 'settings', 'sort_order', 'is_active'];

    protected $casts = [
        'settings'  => 'array',
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(PaymentProviderTranslation::class);
    }

    /**
     * settings.roles — ролі, яким доступний спосіб (напр. відстрочка лише бізнес-клієнтам).
     * Порожньо — доступний усім, зокрема гостям.
     */
    public function isAvailableFor(?User $user): bool
    {
        $roles = $this->settings['roles'] ?? [];

        return ! $roles || ($user && in_array($user->role, $roles, true));
    }

    /** Активні способи оплати, доступні користувачу. */
    public static function availableFor(?User $user)
    {
        return static::with('translations')->where('is_active', true)->orderBy('sort_order')->get()
            ->filter(fn(self $p) => $p->isAvailableFor($user))
            ->values();
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class ShippingProvider extends Model
{
    use BelongsToStore, HasTranslatedName;

    protected $fillable = ['store_id', 'driver', 'settings', 'sort_order', 'is_active'];

    protected $casts = [
        'settings'  => 'array',
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(ShippingProviderTranslation::class);
    }

    /** Чи треба клієнту вказувати місто/відділення (для самовивозу — ні). */
    public function requiresAddress(): bool
    {
        return (bool) ($this->settings['requires_address'] ?? true);
    }

    /** Активні способи доставки для сторінки оформлення. */
    public static function available()
    {
        return static::with('translations')->where('is_active', true)->orderBy('sort_order')->get();
    }
}

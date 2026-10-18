<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use BelongsToStore, HasTranslatedName;

    public const LAYOUTS = ['landing', 'document'];

    /** Адреси, зайняті іншими розділами сайту, — сторінці їх не дати. */
    public const RESERVED_SLUGS = [
        'admin', 'catalog', 'p', 'search', 'cart', 'checkout', 'order', 'brands', 'blog', 'sale', 'services',
        'contacts', 'vacancies', 'login', 'register', 'account', 'auth', 'reset-password', 'email-confirm', 'storage', 'build', 'livewire', 'up', 'forms',
    ];

    protected $fillable = ['store_id', 'key', 'layout', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(PageTranslation::class);
    }

    /** Заголовок поточною мовою (для адмінки й посилань). */
    public function getNameAttribute(): string
    {
        return $this->currentTranslation()?->title ?? (string) $this->key;
    }
}

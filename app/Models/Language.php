<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['store_id', 'code', 'name', 'is_default', 'is_active'];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function store() {
        return $this->belongsTo(Store::class);
    }

    // Повертає ID мови для поточної локалі (з кешем на час запиту)
    public static function currentId(): int
    {
        static $cache = [];
        $locale = app()->getLocale();
        if (!isset($cache[$locale])) {
            $cache[$locale] = static::where('code', $locale)
                ->where('store_id', 1)
                ->value('id') ?? 1;
        }
        return $cache[$locale];
    }
}

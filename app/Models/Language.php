<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    use BelongsToStore;

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
                ->value('id') ?? 1;
        }
        return $cache[$locale];
    }
}

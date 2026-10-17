<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use BelongsToStore, HasTranslatedName;

    protected $fillable = ['store_id', 'icon', 'image', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function translations()
    {
        return $this->hasMany(ServiceTranslation::class);
    }

    public function getNameAttribute(): string
    {
        return $this->currentTranslation()?->title ?? '';
    }
}

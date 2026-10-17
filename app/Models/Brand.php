<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'logo', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    public function translations()
    {
        return $this->hasMany(BrandTranslation::class);
    }

    public function media()
    {
        return $this->hasMany(BrandMedia::class)->orderBy('sort_order')->orderBy('id');
    }

    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->logo) : null;
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['store_id', 'logo', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function translations()
    {
        return $this->hasMany(BrandTranslation::class);
    }

    public function media()
    {
        return $this->hasMany(BrandMedia::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

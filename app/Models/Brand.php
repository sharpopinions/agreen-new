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
        return $this->hasMany(BrandMedia::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockStatus extends Model
{
    protected $fillable = ['store_id', 'code', 'color', 'bg_color', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function translations()
    {
        return $this->hasMany(StockStatusTranslation::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = ['product_id', 'path', 'alt', 'sort_order', 'is_main'];

    protected $casts = [
        'is_main' => 'boolean',
    ];

    /** Публічний URL файлу (диск public). */
    public function getUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::disk('public')->url($this->path);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

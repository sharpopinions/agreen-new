<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandMedia extends Model
{
    protected $fillable = ['brand_id', 'type', 'path', 'title', 'sort_order'];

    /** image — фото, pdf — документ (будь-який файл), video — ID ролика YouTube у path. */
    public const TYPES = ['image', 'video', 'pdf'];

    public function getUrlAttribute(): ?string
    {
        return $this->type === 'video' ? null : \Illuminate\Support\Facades\Storage::disk('public')->url($this->path);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}

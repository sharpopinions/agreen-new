<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BrandMedia extends Model
{
    protected $fillable = ['brand_id', 'type', 'path', 'title', 'sort_order'];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}

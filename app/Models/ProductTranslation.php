<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductTranslation extends Model
{
    protected $fillable = ['product_id', 'language_id', 'name', 'slug', 'description', 'warning_text', 'meta_title', 'meta_description'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromotionTranslation extends Model
{
    protected $fillable = ['promotion_id', 'language_id', 'title', 'slug', 'description', 'meta_title', 'meta_description'];

    public function promotion()
    {
        return $this->belongsTo(Promotion::class);
    }
}

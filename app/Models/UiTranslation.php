<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UiTranslation extends Model
{
    protected $fillable = ['store_id', 'key', 'group'];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function values()
    {
        return $this->hasMany(UiTranslationValue::class);
    }
}

<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class UiTranslation extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'key', 'group'];


    public function values()
    {
        return $this->hasMany(UiTranslationValue::class);
    }
}

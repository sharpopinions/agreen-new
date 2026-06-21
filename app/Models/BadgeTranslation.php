<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BadgeTranslation extends Model
{
    protected $fillable = ['badge_id', 'language_id', 'name'];

    public function badge()
    {
        return $this->belongsTo(Badge::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

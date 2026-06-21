<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UiTranslationValue extends Model
{
    public $timestamps = false;

    protected $fillable = ['ui_translation_id', 'locale', 'value'];
}

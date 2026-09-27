<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValueTranslation extends Model
{
    protected $fillable = ['attribute_value_id', 'language_id', 'name', 'value'];

    public function attributeValue()
    {
        return $this->belongsTo(AttributeValue::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

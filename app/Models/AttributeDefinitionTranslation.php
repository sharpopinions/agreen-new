<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeDefinitionTranslation extends Model
{
    protected $fillable = ['attribute_definition_id', 'language_id', 'name'];

    public function attributeDefinition()
    {
        return $this->belongsTo(AttributeDefinition::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}

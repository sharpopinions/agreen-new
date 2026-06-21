<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    protected $fillable = ['attribute_definition_id', 'sort_order'];

    public function attributeDefinition()
    {
        return $this->belongsTo(AttributeDefinition::class);
    }

    public function translations()
    {
        return $this->hasMany(AttributeValueTranslation::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_attribute_values');
    }
}

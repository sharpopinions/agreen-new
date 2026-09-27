<?php

namespace App\Models;

use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasTranslatedName;

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

    /** Технічне значення (число для range, HEX для кольору) — однакове для всіх мов. */
    public function getRawAttribute(): ?string
    {
        return $this->translations->first()?->value;
    }
}

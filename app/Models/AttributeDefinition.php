<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

/**
 * Характеристика товару (EAV): «Зернистість», «Обʼєм», «Колір»…
 * Значення — attribute_values (назва перекладається, value — число чи HEX для типів number/color).
 */
class AttributeDefinition extends Model
{
    use BelongsToStore, HasTranslatedName;

    public const TYPES = ['select', 'number', 'color', 'boolean', 'text'];

    /** Вигляд фільтра в категорії, типовий для кожного типу. */
    public const DEFAULT_DISPLAY = [
        'select'  => 'checkbox',
        'number'  => 'range',
        'color'   => 'color_swatch',
        'boolean' => 'boolean',
        'text'    => 'checkbox',
    ];

    public const DISPLAY_TYPES = ['checkbox', 'range', 'color_swatch', 'boolean'];

    protected $fillable = ['store_id', 'type', 'is_filterable', 'is_comparable', 'sort_order', 'is_active'];

    protected $casts = [
        'is_filterable' => 'boolean',
        'is_comparable' => 'boolean',
        'is_active'     => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(AttributeDefinitionTranslation::class);
    }

    public function values()
    {
        return $this->hasMany(AttributeValue::class)->orderBy('sort_order')->orderBy('id');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_attribute_definitions')
            ->withPivot(['display_type', 'sort_order']);
    }
}

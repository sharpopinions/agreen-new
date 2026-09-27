<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'parent_id', 'image', 'sort_order', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];


    /** ID усіх нащадків (щоб не зробити категорію дочірньою самій собі). */
    public function descendantIds(): array
    {
        $ids = [];
        $level = [$this->id];
        while ($level) {
            $level = static::whereIn('parent_id', $level)->pluck('id')->all();
            $ids = [...$ids, ...$level];
        }

        return $ids;
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function translations()
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function attributeDefinitions()
    {
        return $this->belongsToMany(AttributeDefinition::class, 'category_attribute_definitions');
    }
}

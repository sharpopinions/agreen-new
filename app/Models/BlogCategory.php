<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Model;

class BlogCategory extends Model
{
    use BelongsToStore, HasTranslatedName;

    protected $fillable = ['store_id', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function translations()
    {
        return $this->hasMany(BlogCategoryTranslation::class);
    }

    public function posts()
    {
        return $this->hasMany(BlogPost::class);
    }
}

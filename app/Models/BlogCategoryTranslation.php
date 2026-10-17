<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogCategoryTranslation extends Model
{
    protected $fillable = ['blog_category_id', 'language_id', 'name', 'slug'];
}

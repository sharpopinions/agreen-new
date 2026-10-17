<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

/** Відгук клієнта (сторінка «Послуги»). */
class Testimonial extends Model
{
    use BelongsToStore;

    protected $fillable = ['store_id', 'author', 'text', 'rating', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];
}

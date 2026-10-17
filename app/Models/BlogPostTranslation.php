<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlogPostTranslation extends Model
{
    protected $fillable = [
        'blog_post_id', 'language_id', 'title', 'slug', 'excerpt', 'body',
        'event_place', 'event_price', 'program', 'speakers', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'program'  => 'array',
        'speakers' => 'array',
    ];

    /** Хвилин читання: ~180 слів за хвилину. */
    public function readingMinutes(): int
    {
        $words = count(preg_split('/\s+/u', trim(strip_tags((string) $this->body)), -1, PREG_SPLIT_NO_EMPTY));

        return max(1, (int) ceil($words / 180));
    }
}

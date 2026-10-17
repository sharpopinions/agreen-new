<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    protected $fillable = [
        'service_id', 'language_id', 'title', 'slug', 'summary', 'details', 'body',
        'stats', 'benefits', 'courses', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'details'  => 'array',
        'stats'    => 'array',
        'benefits' => 'array',
        'courses'  => 'array',
    ];
}

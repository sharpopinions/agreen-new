<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class BlogPost extends Model
{
    use BelongsToStore, HasTranslatedName;

    public const TYPES = ['article', 'event'];

    protected $fillable = [
        'store_id', 'blog_category_id', 'type', 'image', 'published_at',
        'event_starts_at', 'event_ends_at', 'event_seats', 'is_active',
    ];

    protected $casts = [
        'published_at'    => 'datetime',
        'event_starts_at' => 'datetime',
        'event_ends_at'   => 'datetime',
        'is_active'       => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(BlogPostTranslation::class);
    }

    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function getNameAttribute(): string
    {
        return $this->currentTranslation()?->title ?? '';
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::disk('public')->url($this->image) : null;
    }

    public function isEvent(): bool
    {
        return $this->type === 'event';
    }

    /** Опубліковані: увімкнені й з датою публікації не в майбутньому. */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }
}

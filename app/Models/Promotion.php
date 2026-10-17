<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use App\Models\Concerns\HasTranslatedName;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    use BelongsToStore, HasTranslatedName;

    protected $fillable = [
        'store_id', 'brand_id', 'category_id', 'discount_percent', 'image',
        'starts_at', 'ends_at', 'sort_order', 'is_active',
    ];

    protected $casts = [
        'starts_at' => 'date',
        'ends_at'   => 'date',
        'is_active' => 'boolean',
    ];

    public function translations()
    {
        return $this->hasMany(PromotionTranslation::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }

    public function getNameAttribute(): string
    {
        return $this->currentTranslation()?->title ?? '';
    }

    /** Діє сьогодні: увімкнена, вже почалась і ще не закінчилась. */
    public function scopeRunning(Builder $query): Builder
    {
        $today = now()->toDateString();

        return $query->where('is_active', true)
            ->where(fn($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', $today))
            ->where(fn($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', $today));
    }

    /**
     * Обмеження вибірки товарів акцією: вибрані вручну, а якщо їх немає —
     * усі товари бренду та/або категорії акції (разом з підкатегоріями).
     */
    public function constrainProducts(Builder $query): Builder
    {
        if ($this->products()->exists()) {
            return $query->whereHas('promotions', fn($q) => $q->whereKey($this->id));
        }

        if (! $this->brand_id && ! $this->category_id) {
            return $query->whereRaw('1 = 0');
        }

        return $query
            ->when($this->brand_id, fn($q) => $q->where('brand_id', $this->brand_id))
            ->when($this->category_id, fn($q) => $q->whereHas('categories', fn($c) => $c->whereIn('categories.id',
                Category::where('parent_id', $this->category_id)->pluck('id')->push($this->category_id))));
    }
}

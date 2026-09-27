<?php

namespace App\Models;

use App\Models\Concerns\BelongsToStore;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use BelongsToStore;

    protected $fillable = [
        'store_id',
        'brand_id',
        'stock_status_id',
        'sku',
        'price',
        'old_price',
        'stock_quantity',
        'preorder_days',
        'replaced_by_id',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price'     => 'decimal:2',
        'old_price' => 'decimal:2',
        'is_active' => 'boolean',
    ];


    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /** Є залишок на складі (або статус «В наявності», якщо залишок невідомий). */
    public function isInStock(): bool
    {
        return $this->stock_quantity !== null
            ? $this->stock_quantity > 0
            : $this->stockStatus?->code === 'in_stock';
    }

    /** Товар, що замінює знятий з виробництва артикул. */
    public function replacedBy()
    {
        return $this->belongsTo(Product::class, 'replaced_by_id');
    }

    public function stockStatus()
    {
        return $this->belongsTo(StockStatus::class);
    }

    public function translations()
    {
        return $this->hasMany(ProductTranslation::class);
    }

    /** Фото за порядком; перше — головне (картка товару, og:image). */
    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order')->orderBy('id');
    }

    public function videos()
    {
        return $this->hasMany(ProductVideo::class)->orderBy('sort_order');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function badges()
    {
        return $this->belongsToMany(Badge::class);
    }

    public function attributeValues()
    {
        return $this->belongsToMany(AttributeValue::class, 'product_attribute_values');
    }

    /** Активні товари з усім, що потрібно картці (App\Presenters\ProductCard). */
    public function scopeForCard($query)
    {
        return $query->with(\App\Presenters\ProductCard::relations())->where('is_active', true);
    }
}

<?php

namespace App\Presenters;

use App\Models\Language;
use App\Models\Product;

/**
 * Дані картки товару для фронтенду (ProductCard.vue): каталог, головна, «схожі», кошик.
 * Запит готує Product::query()->forCard() — підвантажує переклади поточною мовою.
 */
class ProductCard
{
    /** Зв'язки, потрібні картці (переклади — лише поточної мови). */
    public static function relations(?int $langId = null): array
    {
        $langId ??= Language::currentId();
        $tr = fn($q) => $q->where('language_id', $langId);

        return [
            'translations'        => $tr,
            'brand.translations'  => $tr,
            'badges.translations' => $tr,
            'images',
        ];
    }

    public static function make(Product $p): array
    {
        $translation = $p->translations->first();
        $badge       = $p->badges->first();

        return [
            'id'       => $p->id,
            'name'     => $translation?->name ?? '',
            'slug'     => $translation?->slug ?? '',
            'sku'      => $p->sku,
            'image'    => $p->images->first()?->url,
            'price'    => (float) $p->price,
            'oldPrice' => $p->old_price ? (float) $p->old_price : null,
            'brand'    => $p->brand?->translations->first()?->name,
            'badge'    => $badge ? [
                'name'    => $badge->translations->first()?->name ?? '',
                'color'   => $badge->color,
                'bgColor' => $badge->bg_color,
            ] : null,
            'rating'   => (float) $p->rating,
            'reviews'  => $p->reviews_count,
            'stock'    => $p->stock_quantity,
        ];
    }

    /** @param iterable<Product> $products */
    public static function collection(iterable $products): array
    {
        return collect($products)->map(fn(Product $p) => self::make($p))->values()->all();
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function show(string $slug): Response
    {
        $langId = Language::currentId();

        $product = Product::with([
            'translations'                                     => fn($q) => $q->where('language_id', $langId),
            'brand.translations'                               => fn($q) => $q->where('language_id', $langId),
            'badges.translations'                              => fn($q) => $q->where('language_id', $langId),
            'categories.translations'                          => fn($q) => $q->where('language_id', $langId),
            'attributeValues.translations'                     => fn($q) => $q->where('language_id', $langId),
            'attributeValues.attributeDefinition.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->whereHas('translations', fn($q) => $q->where('slug', $slug)->where('language_id', $langId))
            ->firstOrFail();

        $translation = $product->translations->first();
        $category    = $product->categories->first();

        $related = Product::with([
            'translations'        => fn($q) => $q->where('language_id', $langId),
            'brand.translations'  => fn($q) => $q->where('language_id', $langId),
            'badges.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->where('id', '!=', $product->id)
            ->when($category, fn($q) => $q->whereHas('categories', fn($q2) => $q2->where('categories.id', $category->id)))
            ->limit(8)
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->translations->first()?->name ?? '',
                'slug'     => $p->translations->first()?->slug ?? '',
                'sku'      => $p->sku,
                'price'    => (float) $p->price,
                'oldPrice' => $p->old_price ? (float) $p->old_price : null,
                'brand'    => $p->brand?->translations->first()?->name ?? null,
                'badge'    => $p->badges->first() ? [
                    'name'    => $p->badges->first()->translations->first()?->name ?? '',
                    'color'   => $p->badges->first()->color,
                    'bgColor' => $p->badges->first()->bg_color,
                ] : null,
                'rating'   => (float) $p->rating,
                'reviews'  => $p->reviews_count,
                'stock'    => $p->stock_quantity,
            ]);

        return Inertia::render('Product', [
            'product' => [
                'id'          => $product->id,
                'name'        => $translation?->name ?? '',
                'slug'        => $slug,
                'sku'         => $product->sku,
                'price'       => (float) $product->price,
                'oldPrice'    => $product->old_price ? (float) $product->old_price : null,
                'description' => $translation?->description ?? '',
                'rating'      => (float) $product->rating,
                'reviews'     => $product->reviews_count,
                'stock'       => $product->stock_quantity,
                'brand'       => $product->brand ? [
                    'name' => $product->brand->translations->first()?->name ?? '',
                    'slug' => $product->brand->translations->first()?->slug ?? '',
                ] : null,
                'badges'      => $product->badges->map(fn($b) => [
                    'name'    => $b->translations->first()?->name ?? '',
                    'color'   => $b->color,
                    'bgColor' => $b->bg_color,
                ])->values(),
                'attributes'  => $product->attributeValues->map(fn($av) => [
                    'name'  => $av->attributeDefinition->translations->first()?->name ?? '',
                    'value' => $av->translations->first()?->value ?? '',
                ])->filter(fn($a) => $a['name'] && $a['value'])->values(),
                'category'    => $category ? [
                    'name' => $category->translations->first()?->name ?? '',
                    'slug' => $category->translations->first()?->slug ?? '',
                ] : null,
            ],
            'related' => $related,
        ]);
    }
}

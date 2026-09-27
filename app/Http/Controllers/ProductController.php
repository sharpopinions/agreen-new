<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Presenters\ProductCard;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function show(string $slug): Response
    {
        $langId = Language::currentId();
        $tr     = fn($q) => $q->where('language_id', $langId);

        $product = Product::with([
            'translations'                                     => $tr,
            'brand.translations'                               => $tr,
            'badges.translations'                              => $tr,
            'categories.translations'                          => $tr,
            'categories.parent.translations'                   => $tr,
            'attributeValues.translations'                     => $tr,
            'attributeValues.attributeDefinition.translations' => $tr,
            'stockStatus',
            'replacedBy.translations'                          => $tr,
            'images'                                           => fn($q) => $q->orderByDesc('is_main')->orderBy('sort_order'),
            'videos'                                           => fn($q) => $q->orderBy('sort_order'),
        ])
            ->where('is_active', true)
            ->whereHas('translations', fn($q) => $q->where('slug', $slug)->where('language_id', $langId))
            ->firstOrFail();

        $translation = $product->translations->first();
        $category    = $product->categories->first();

        $related = ProductCard::collection(Product::query()->forCard()
            ->where('id', '!=', $product->id)
            ->when($category, fn($q) => $q->whereHas('categories', fn($q2) => $q2->where('categories.id', $category->id)))
            ->limit(8)
            ->get());

        $replacement = $product->replacedBy?->is_active ? $product->replacedBy : null;

        return Inertia::render('Product', [
            'product' => [
                'id'           => $product->id,
                'name'         => $translation?->name ?? '',
                'slug'         => $slug,
                'sku'          => $product->sku,
                'price'        => (float) $product->price,
                'oldPrice'     => $product->old_price ? (float) $product->old_price : null,
                // Персональна ціна бізнес-клієнта — з'явиться разом з авторизацією та цінами з 1С
                'partnerPrice' => null,
                'description'  => $translation?->description ?? '',
                'warning'      => trim((string) ($translation?->warning_text ?? '')) ?: null,
                'rating'       => (float) $product->rating,
                'reviews'      => $product->reviews_count,
                'stock'        => $product->stock_quantity,
                'availability' => $this->availability($product, $replacement !== null),
                'preorderDays' => $product->preorder_days,
                'replacement'  => $replacement ? [
                    'name' => $replacement->translations->first()?->name ?? '',
                    'slug' => $replacement->translations->first()?->slug ?? '',
                    'sku'  => $replacement->sku,
                ] : null,
                'images'       => $product->images->map(fn($i) => [
                    'url' => Storage::url($i->path),
                    'alt' => $i->alt ?? '',
                ])->values(),
                'videos'       => $product->videos->pluck('youtube_id')->values(),
                'brand'        => $product->brand ? [
                    'id'   => $product->brand->id,
                    'name' => $product->brand->translations->first()?->name ?? '',
                    'slug' => $product->brand->translations->first()?->slug ?? '',
                ] : null,
                'badges'       => $product->badges->map(fn($b) => [
                    'name'    => $b->translations->first()?->name ?? '',
                    'color'   => $b->color,
                    'bgColor' => $b->bg_color,
                ])->values(),
                'attributes'   => $product->attributeValues->map(fn($av) => [
                    'name'  => $av->attributeDefinition->translations->first()?->name ?? '',
                    'value' => $av->translations->first()?->value ?? '',
                ])->filter(fn($a) => $a['name'] && $a['value'])->values(),
                'breadcrumbs'  => $category ? $this->categoryTrail($category) : [],
            ],
            'related' => $related,
        ]);
    }

    /**
     * in_stock — є залишок; on_order — під замовлення (ціну не показуємо, за ТЗ);
     * discontinued — знято з виробництва і є заміна.
     */
    private function availability(Product $product, bool $hasReplacement): string
    {
        $inStock = $product->isInStock();

        if (! $inStock && $hasReplacement) {
            return 'discontinued';
        }

        return $inStock ? 'in_stock' : 'on_order';
    }

    /** Ланцюжок категорій від кореня до категорії товару. */
    private function categoryTrail(Category $category): array
    {
        $trail = [];
        for ($c = $category; $c; $c = $c->parent) {
            array_unshift($trail, [
                'name' => $c->translations->first()?->name ?? '',
                'slug' => $c->translations->first()?->slug ?? '',
            ]);
        }

        return $trail;
    }
}

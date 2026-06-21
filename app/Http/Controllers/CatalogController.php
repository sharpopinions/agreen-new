<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Brand;
use App\Models\Language;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(Request $request): Response
    {
        $langId   = Language::currentId();
        $brandIds = array_filter(array_map('intval', (array) $request->get('brand', [])));
        $sort     = $request->get('sort', 'default');
        $minPrice = $request->get('min_price') ? (float) $request->get('min_price') : null;
        $maxPrice = $request->get('max_price') ? (float) $request->get('max_price') : null;

        return Inertia::render('Catalog', [
            'categories'   => $this->getCategories($langId),
            'brands'       => $this->getBrands($langId),
            'products'     => $this->getProducts($langId, 0, $brandIds, $sort, $minPrice, $maxPrice),
            'activeBrands' => array_values($brandIds),
            'currentSort'  => $sort,
            'minPrice'     => $minPrice,
            'maxPrice'     => $maxPrice,
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $langId = Language::currentId();

        $category = Category::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->whereHas('translations', fn($q) => $q->where('slug', $slug))
            ->firstOrFail();

        $translation = $category->translations->first();
        $brandIds    = array_filter(array_map('intval', (array) $request->get('brand', [])));
        $sort        = $request->get('sort', 'default');
        $minPrice    = $request->get('min_price') ? (float) $request->get('min_price') : null;
        $maxPrice    = $request->get('max_price') ? (float) $request->get('max_price') : null;

        return Inertia::render('CatalogCategory', [
            'category'     => ['id' => $category->id, 'name' => $translation?->name ?? '', 'slug' => $slug],
            'categories'   => $this->getCategories($langId),
            'brands'       => $this->getBrands($langId),
            'products'     => $this->getProducts($langId, $category->id, $brandIds, $sort, $minPrice, $maxPrice),
            'activeBrands' => array_values($brandIds),
            'currentSort'  => $sort,
            'minPrice'     => $minPrice,
            'maxPrice'     => $maxPrice,
        ]);
    }

    private function getCategories(int $langId): array
    {
        return Category::with([
            'translations'          => fn($q) => $q->where('language_id', $langId),
            'children.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->withCount(['products' => fn($q) => $q->where('store_id', 1)->where('is_active', true)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($cat) => [
                'id'       => $cat->id,
                'name'     => $cat->translations->first()?->name ?? '',
                'slug'     => $cat->translations->first()?->slug ?? '',
                'count'    => $cat->products_count,
                'children' => $cat->children
                    ->filter(fn($c) => $c->is_active)
                    ->map(fn($sub) => [
                        'id'   => $sub->id,
                        'name' => $sub->translations->first()?->name ?? '',
                        'slug' => $sub->translations->first()?->slug ?? '',
                    ])->values(),
            ])
            ->toArray();
    }

    private function getBrands(int $langId): array
    {
        return Brand::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'name' => $b->translations->first()?->name ?? '',
            ])
            ->toArray();
    }

    private function getProducts(int $langId, int $categoryId, array $brandIds, string $sort, ?float $minPrice = null, ?float $maxPrice = null): object
    {
        $query = Product::with([
            'translations'        => fn($q) => $q->where('language_id', $langId),
            'brand.translations'  => fn($q) => $q->where('language_id', $langId),
            'badges.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('store_id', 1)
            ->where('is_active', true);

        if ($categoryId) {
            $query->whereHas('categories', fn($q) => $q->where('categories.id', $categoryId));
        }

        if (!empty($brandIds)) {
            $query->whereIn('brand_id', $brandIds);
        }

        if ($minPrice !== null) {
            $query->where('price', '>=', $minPrice);
        }

        if ($maxPrice !== null) {
            $query->where('price', '<=', $maxPrice);
        }

        match ($sort) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderBy('price', 'desc'),
            default      => $query->orderBy('sort_order'),
        };

        return $query->paginate(12)->through(fn($p) => [
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
    }
}

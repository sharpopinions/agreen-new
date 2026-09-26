<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    /** Каталог: плитки категорій + усі товари з фільтрами. */
    public function index(Request $request): Response
    {
        return $this->listing($request, Language::currentId(), null, 'Catalog');
    }

    /** Усі товари з фільтрами (Figma: Catalog//All-products). */
    public function all(Request $request): Response
    {
        return $this->listing($request, Language::currentId(), null, 'CatalogCategory');
    }

    /** Категорія або підкатегорія (Figma: Catalog-category, Catalog//Subcategory). */
    public function show(Request $request, string $slug): Response
    {
        $langId = Language::currentId();

        $category = Category::with([
            'translations'          => fn($q) => $q->where('language_id', $langId),
            'parent.translations'   => fn($q) => $q->where('language_id', $langId),
            'children.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->whereHas('translations', fn($q) => $q->where('slug', $slug))
            ->firstOrFail();

        return $this->listing($request, $langId, $category, 'CatalogCategory');
    }

    private function listing(Request $request, int $langId, ?Category $category, string $page): Response
    {
        $filters = [
            'q'            => trim((string) $request->get('q', '')),
            'brand'        => array_values(array_filter(array_map('intval', (array) $request->get('brand', [])))),
            'category'     => array_values(array_filter(array_map('intval', (array) $request->get('category', [])))),
            'availability' => array_values(array_intersect((array) $request->get('availability', []), ['in_stock', 'preorder'])),
            'sale'         => $request->boolean('sale'),
            'min_price'    => $request->filled('min_price') ? (float) $request->get('min_price') : null,
            'max_price'    => $request->filled('max_price') ? (float) $request->get('max_price') : null,
            'sort'         => in_array($request->get('sort'), ['price_asc', 'price_desc'], true) ? $request->get('sort') : 'popular',
        ];

        // Базова вибірка — товари категорії (разом з її підкатегоріями) або всі
        $scopeIds = $category
            ? $category->children->where('is_active', true)->pluck('id')->push($category->id)->all()
            : null;

        $base = fn() => $this->productQuery($langId)
            ->when($scopeIds, fn($q) => $q->whereHas('categories', fn($c) => $c->whereIn('categories.id', $scopeIds)));

        $query = $base();
        $this->applyFilters($query, $filters);

        match ($filters['sort']) {
            'price_asc'  => $query->orderBy('price'),
            'price_desc' => $query->orderByDesc('price'),
            default      => $query->orderByDesc('reviews_count')->orderBy('sort_order'),
        };

        $products = $query->paginate(12)->withQueryString()->through(fn($p) => $this->mapProduct($p));

        // Фільтр «Категорія» / «Підкатегорія»: для категорії — її діти, інакше — кореневі
        $filterCategories = $category
            ? $category->children->where('is_active', true)->map(fn($c) => [
                'id'    => $c->id,
                'name'  => $c->translations->first()?->name ?? '',
                'slug'  => $c->translations->first()?->slug ?? '',
                'count' => $base()->whereHas('categories', fn($q) => $q->where('categories.id', $c->id))->count(),
            ])->values()->all()
            : $this->getCategories($langId);

        $prices = $base()->selectRaw('MIN(price) as min, MAX(price) as max')->reorder()->first();

        return Inertia::render($page, [
            'category'   => $category ? [
                'id'     => $category->id,
                'name'   => $category->translations->first()?->name ?? '',
                'slug'   => $category->translations->first()?->slug ?? '',
                'parent' => $category->parent ? [
                    'name' => $category->parent->translations->first()?->name ?? '',
                    'slug' => $category->parent->translations->first()?->slug ?? '',
                ] : null,
            ] : null,
            'categories' => $filterCategories,
            'brands'     => $this->getBrands($langId, $base()),
            'products'   => $products,
            'filters'    => $filters,
            'priceRange' => ['min' => (float) ($prices->min ?? 0), 'max' => (float) ($prices->max ?? 0)],
        ]);
    }

    private function applyFilters(Builder $query, array $f): void
    {
        $query
            ->when($f['q'] !== '', fn($q) => $q->where(fn($w) => $w
                ->where('sku', 'like', '%' . $f['q'] . '%')
                ->orWhereHas('translations', fn($t) => $t->where('name', 'like', '%' . $f['q'] . '%'))))
            ->when($f['brand'], fn($q) => $q->whereIn('brand_id', $f['brand']))
            ->when($f['category'], fn($q) => $q->whereHas('categories', fn($c) => $c->whereIn('categories.id', $f['category'])))
            ->when($f['min_price'] !== null, fn($q) => $q->where('price', '>=', $f['min_price']))
            ->when($f['max_price'] !== null, fn($q) => $q->where('price', '<=', $f['max_price']))
            ->when($f['sale'], fn($q) => $q->whereNotNull('old_price'))
            ->when(count($f['availability']) === 1, fn($q) => $f['availability'][0] === 'in_stock'
                ? $q->where('stock_quantity', '>', 0)
                : $q->where(fn($s) => $s->whereNull('stock_quantity')->orWhere('stock_quantity', 0)));
    }

    private function productQuery(int $langId): Builder
    {
        return Product::with([
            'translations'        => fn($q) => $q->where('language_id', $langId),
            'brand.translations'  => fn($q) => $q->where('language_id', $langId),
            'badges.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('store_id', 1)
            ->where('is_active', true);
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

    /** Бренди з кількістю товарів у поточній вибірці. */
    private function getBrands(int $langId, Builder $scope): array
    {
        $counts = $scope->reorder()->toBase()
            ->selectRaw('brand_id, COUNT(*) as cnt')
            ->whereNotNull('brand_id')
            ->groupBy('brand_id')
            ->pluck('cnt', 'brand_id');

        return Brand::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($b) => [
                'id'    => $b->id,
                'name'  => $b->translations->first()?->name ?? '',
                'slug'  => $b->translations->first()?->slug ?? '',
                'count' => (int) ($counts[$b->id] ?? 0),
            ])
            ->toArray();
    }

    private function mapProduct(Product $p): array
    {
        $badge = $p->badges->first();

        return [
            'id'       => $p->id,
            'name'     => $p->translations->first()?->name ?? '',
            'slug'     => $p->translations->first()?->slug ?? '',
            'sku'      => $p->sku,
            'price'    => (float) $p->price,
            'oldPrice' => $p->old_price ? (float) $p->old_price : null,
            'brand'    => $p->brand?->translations->first()?->name ?? null,
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
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Presenters\ProductCard;
use App\Services\Cart;
use App\Services\ProductLists;
use App\Support\Seo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Обрані товари (Claude Design → FavoritesPage, FavoritePopup) і порівняння (ComparePage).
 */
class ProductListController extends Controller
{
    public function __construct(private ProductLists $lists) {}

    // ── Вішліст ────────────────────────────────────────────────

    public function wishlist(): Response
    {
        return Inertia::render('Wishlist', [
            'products' => ProductCard::collection($this->products('wishlist')),
            'seo'      => Seo::make('Обрані товари', noindex: true),
        ]);
    }

    public function toggleWishlist(Product $product): RedirectResponse
    {
        $this->lists->has('wishlist', $product->id)
            ? $this->lists->remove('wishlist', $product->id)
            : $this->lists->add('wishlist', $product->id);

        return back();
    }

    public function clearWishlist(): RedirectResponse
    {
        $this->lists->clear('wishlist');

        return back();
    }

    /** «Додати все до кошика»: товари під замовлення теж ідуть у кошик (окремим замовленням). */
    public function wishlistToCart(Cart $cart): RedirectResponse
    {
        $products = $this->products('wishlist', ['stockStatus']);
        foreach ($products as $product) {
            $cart->add($product);
        }

        return redirect()->route('cart')->with('notice', 'Обрані товари додано до кошика.');
    }

    /** Попап у хедері: перші 4 товари (завантажується при наведенні). */
    public function preview(): JsonResponse
    {
        $products = $this->products('wishlist');

        return response()->json([
            'count' => $products->count(),
            'items' => ProductCard::collection($products->take(4)),
        ]);
    }

    // ── Порівняння ─────────────────────────────────────────────

    public function compare(Request $request): Response
    {
        $langId   = Language::currentId();
        $tr       = fn($q) => $q->where('language_id', $langId);
        $products = $this->products('compare', [
            'categories.translations'                          => $tr,
            'categories.parent.translations'                   => $tr,
            'attributeValues.translations'                     => $tr,
            'attributeValues.attributeDefinition.translations' => $tr,
            'stockStatus.translations'                         => $tr,
        ]);

        // Порівнюємо товари однієї кореневої категорії — перемикач категорій над таблицею
        $groups = $products->groupBy(fn(Product $p) => $this->rootCategory($p)?->id ?? 0);
        $tabs   = $groups->map(function (Collection $items, $id) {
            $root = $this->rootCategory($items->first());

            return ['id' => (int) $id, 'name' => $root?->translations->first()?->name ?? 'Інше', 'count' => $items->count()];
        })->values();

        $active = (int) $request->query('category', $tabs->first()['id'] ?? 0);
        $items  = $groups->get($active) ?? $groups->first() ?? collect();
        $active = $items->isNotEmpty() ? ($this->rootCategory($items->first())?->id ?? 0) : null;

        return Inertia::render('Compare', [
            'tabs'     => $tabs,
            'category' => $active,
            'products' => $items->map(fn(Product $p) => ProductCard::make($p))->values(),
            'rows'     => $this->rows($items),
            'limit'    => ProductLists::COMPARE_PER_CATEGORY,
            'seo'      => Seo::make('Порівняння товарів', noindex: true),
        ]);
    }

    public function toggleCompare(Product $product): RedirectResponse
    {
        if ($this->lists->has('compare', $product->id)) {
            $this->lists->remove('compare', $product->id);

            return back();
        }

        $root = $this->rootCategory($product->load('categories.parent'))?->id;
        $sameCategory = Product::with('categories.parent')->whereIn('id', $this->lists->ids('compare'))->get()
            ->filter(fn(Product $p) => $this->rootCategory($p)?->id === $root)->count();

        if ($sameCategory >= ProductLists::COMPARE_PER_CATEGORY) {
            return back()->with('notice', 'Можна порівнювати до ' . ProductLists::COMPARE_PER_CATEGORY . ' товарів однієї категорії. Приберіть один із порівняння.');
        }

        $this->lists->add('compare', $product->id);

        return back()->with('notice', 'Товар додано до порівняння.');
    }

    /** Очистити порівняння (усе або лише одну категорію). */
    public function clearCompare(Request $request): RedirectResponse
    {
        $category = $request->integer('category');
        if (! $category) {
            $this->lists->clear('compare');

            return redirect()->route('compare');
        }

        $ids = $this->products('compare', ['categories.parent'])
            ->filter(fn(Product $p) => $this->rootCategory($p)?->id === $category)
            ->pluck('id')->all();
        $this->lists->remove('compare', $ids);

        return redirect()->route('compare');
    }

    // ── Спільне ────────────────────────────────────────────────

    /** Активні товари списку в порядку додавання (нові — першими). */
    private function products(string $list, array $with = []): Collection
    {
        $ids = $this->lists->ids($list);
        if (! $ids) {
            return collect();
        }

        $products = Product::query()->forCard()->with($with)->whereIn('id', $ids)->get()->keyBy('id');

        return collect($ids)->map(fn($id) => $products->get($id))->filter()->values();
    }

    private function rootCategory(Product $product): ?Category
    {
        $categories = $product->categories;

        return $categories->firstWhere('parent_id', null) ?? $categories->first()?->parent ?? $categories->first();
    }

    private function availability(Product $p): string
    {
        if ($p->isInStock()) {
            return $p->stock_quantity ? "В наявності: {$p->stock_quantity} шт" : 'В наявності';
        }

        return $p->stockStatus?->translations->first()?->name ?? 'Під замовлення';
    }

    /** Рядки таблиці: базові поля + усі характеристики товарів категорії. */
    private function rows(Collection $products): array
    {
        $rows = [
            ['name' => 'Бренд', 'values' => $products->map(fn($p) => $p->brand?->translations->first()?->name ?? '—')->all()],
            ['name' => 'Наявність', 'values' => $products->map(fn($p) => $this->availability($p))->all()],
            ['name' => 'Рейтинг', 'values' => $products->map(fn($p) => $p->rating ? number_format((float) $p->rating, 1) . '/5' : '—')->all()],
        ];

        $definitions = $products->flatMap(fn($p) => $p->attributeValues->pluck('attributeDefinition'))
            ->filter(fn($d) => $d?->is_active)
            ->unique('id')
            ->sortBy('sort_order');

        foreach ($definitions as $definition) {
            $rows[] = [
                'name'   => $definition->name,
                'values' => $products->map(fn($p) => $p->attributeValues
                    ->where('attribute_definition_id', $definition->id)
                    ->sortBy('sort_order')->map->name->implode(', ') ?: '—')->all(),
            ];
        }

        return $rows;
    }
}

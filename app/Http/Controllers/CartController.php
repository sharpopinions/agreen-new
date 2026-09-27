<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Product;
use App\Services\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function index(): Response
    {
        $summary = $this->cart->summary();

        return Inertia::render('Cart', [
            // ТЗ: у порожньому кошику — блок із 3 товарів
            'suggestions' => $summary['count'] === 0 ? $this->popular(3) : [],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity'   => ['nullable', 'integer', 'min:1', 'max:999'],
        ]);

        $product = $this->findProduct($data['product_id']);
        $this->cart->add($product, $data['quantity'] ?? 1);

        // Дані для попапу «Додано до кошика»
        return back()->with('cartAdded', [
            'productId'       => $product->id,
            'recommendations' => $this->related($product, 3),
        ]);
    }

    public function update(Request $request, int $product): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:0', 'max:999']]);
        $this->cart->set($this->findProduct($product), $data['quantity']);

        return back();
    }

    public function destroy(int $product): RedirectResponse
    {
        $this->cart->remove($product);

        return back();
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back();
    }

    private function findProduct(int $id): Product
    {
        return Product::with('stockStatus')
            ->where('store_id', 1)
            ->where('is_active', true)
            ->findOrFail($id);
    }

    private function related(Product $product, int $limit): array
    {
        $categoryIds = $product->categories()->pluck('categories.id');

        return $this->productQuery()
            ->where('id', '!=', $product->id)
            ->when($categoryIds->isNotEmpty(), fn($q) => $q->whereHas('categories', fn($c) => $c->whereIn('categories.id', $categoryIds)))
            ->limit($limit)
            ->get()
            ->map(fn($p) => $this->mapProduct($p))
            ->all();
    }

    private function popular(int $limit): array
    {
        return $this->productQuery()
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get()
            ->map(fn($p) => $this->mapProduct($p))
            ->all();
    }

    private function productQuery()
    {
        $langId = Language::currentId();

        return Product::with([
            'translations'        => fn($q) => $q->where('language_id', $langId),
            'badges.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('store_id', 1)
            ->where('is_active', true);
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

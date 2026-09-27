<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Presenters\ProductCard;
use App\Support\Seo;
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
            'seo'         => Seo::make('Кошик', noindex: true),
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
            ->where('is_active', true)
            ->findOrFail($id);
    }

    private function related(Product $product, int $limit): array
    {
        $categoryIds = $product->categories()->pluck('categories.id');

        return Product::query()->forCard()
            ->where('id', '!=', $product->id)
            ->when($categoryIds->isNotEmpty(), fn($q) => $q->whereHas('categories', fn($c) => $c->whereIn('categories.id', $categoryIds)))
            ->limit($limit)
            ->get()
            ->pipe(fn($products) => ProductCard::collection($products));
    }

    private function popular(int $limit): array
    {
        return Product::query()->forCard()
            ->orderByDesc('reviews_count')
            ->limit($limit)
            ->get()
            ->pipe(fn($products) => ProductCard::collection($products));
    }
}

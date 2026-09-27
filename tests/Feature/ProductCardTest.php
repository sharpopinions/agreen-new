<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Усі сторінки віддають картку товару в однаковому форматі (App\Presenters\ProductCard). */
class ProductCardTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private const KEYS = ['id', 'name', 'slug', 'sku', 'price', 'oldPrice', 'brand', 'badge', 'rating', 'reviews', 'stock'];

    public function test_card_shape_is_the_same_everywhere(): void
    {
        // Товар, у категорії якого є інші товари — щоб блок «Схожі» не був порожнім
        $product = Product::where('is_active', true)->with(['translations', 'categories.products'])->get()
            ->first(fn($p) => $p->categories->first()?->products->where('is_active', true)->count() > 1);
        $slug    = $product->translations->firstWhere('language_id', 1)->slug;

        $this->get('/')->assertInertia(fn($p) => $p->has('products.0', fn($c) => $c->hasAll(self::KEYS)));
        $this->get('/catalog/all')->assertInertia(fn($p) => $p->has('products.data.0', fn($c) => $c->hasAll(self::KEYS)));
        $this->get("/p/{$slug}")->assertInertia(fn($p) => $p->has('related.0', fn($c) => $c->hasAll(self::KEYS)));
    }
}

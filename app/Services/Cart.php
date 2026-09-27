<?php

namespace App\Services;

use App\Models\Language;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

/**
 * Кошик у сесії: [product_id => quantity].
 * Товари під замовлення йдуть окремим замовленням без оплати й доставки (ТЗ).
 */
class Cart
{
    private const KEY = 'cart';

    /** @return array<int, int> */
    public function raw(): array
    {
        return session(self::KEY, []);
    }

    public function add(Product $product, int $qty = 1): void
    {
        $cart = $this->raw();
        $cart[$product->id] = $this->clamp($product, ($cart[$product->id] ?? 0) + max(1, $qty));
        session([self::KEY => $cart]);
    }

    public function set(Product $product, int $qty): void
    {
        $cart = $this->raw();
        if ($qty < 1) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = $this->clamp($product, $qty);
        }
        session([self::KEY => $cart]);
    }

    public function remove(int $productId): void
    {
        $cart = $this->raw();
        unset($cart[$productId]);
        session([self::KEY => $cart]);
    }

    public function clear(): void
    {
        session()->forget(self::KEY);
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    /**
     * Позиції з актуальними даними товару. Неактивні / видалені товари прибираються.
     */
    public function items(): Collection
    {
        $raw = $this->raw();
        if (! $raw) {
            return collect();
        }

        $langId = Language::currentId();

        $products = Product::with([
            'translations' => fn($q) => $q->where('language_id', $langId),
            'images'       => fn($q) => $q->orderByDesc('is_main')->orderBy('sort_order'),
            'stockStatus',
        ])
            ->where('is_active', true)
            ->whereIn('id', array_keys($raw))
            ->get()
            ->keyBy('id');

        // Прибрати з сесії те, чого вже немає
        if ($products->count() !== count($raw)) {
            session([self::KEY => array_intersect_key($raw, $products->all())]);
        }

        return collect($raw)
            ->filter(fn($qty, $id) => $products->has($id))
            ->map(function ($qty, $id) use ($products) {
                $p       = $products[$id];
                $inStock = $p->isInStock();
                $image   = $p->images->first();

                return [
                    'id'       => $p->id,
                    'name'     => $p->translations->first()?->name ?? $p->sku,
                    'slug'     => $p->translations->first()?->slug ?? '',
                    'sku'      => $p->sku,
                    'price'    => (float) $p->price,
                    'quantity' => $qty,
                    'total'    => round((float) $p->price * $qty, 2),
                    'preorder' => ! $inStock,
                    'stock'    => $inStock ? $p->stock_quantity : null,
                    'image'    => $image ? Storage::url($image->path) : null,
                ];
            })
            ->values();
    }

    /** Дані для фронту: позиції, розбиті на звичайні та передзамовлення. */
    public function summary(): array
    {
        $items    = $this->items();
        $regular  = $items->where('preorder', false)->values();
        $preorder = $items->where('preorder', true)->values();

        return [
            'items'         => $items,
            'count'         => $items->sum('quantity'),
            'total'         => round($items->sum('total'), 2),
            'regularTotal'  => round($regular->sum('total'), 2),
            'preorderCount' => $preorder->sum('quantity'),
        ];
    }

    /** Не більше, ніж є на складі (ТЗ); для передзамовлення — без обмеження. */
    private function clamp(Product $product, int $qty): int
    {
        $qty = max(1, min($qty, 999));

        if ($product->isInStock() && $product->stock_quantity !== null) {
            return min($qty, $product->stock_quantity);
        }

        return $qty;
    }
}

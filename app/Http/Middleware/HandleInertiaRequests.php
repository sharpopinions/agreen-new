<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Services\Cart;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Кошик (сесія) — актуальний на кожному запиті
            'cart'  => fn () => app(Cart::class)->summary(),
            'flash' => fn () => [
                'cartAdded' => $request->session()->get('cartAdded'),
            ],
        ]);
    }

    /**
     * Дані для мегаменю хедера. Передаються один раз і запам'ятовуються
     * клієнтом між переходами (Inertia once props).
     */
    public function shareOnce(Request $request): array
    {
        return [
            'nav' => fn () => $this->navigation(),
        ];
    }

    private function navigation(): array
    {
        $langId = Language::currentId();

        $categories = Category::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->withCount(['products' => fn($q) => $q->where('store_id', 1)->where('is_active', true)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($cat) => [
                'id'    => $cat->id,
                'name'  => $cat->translations->first()?->name ?? '',
                'slug'  => $cat->translations->first()?->slug ?? '',
                'count' => $cat->products_count,
            ]);

        $brands = Brand::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'name' => $b->translations->first()?->name ?? '',
            ]);

        return compact('categories', 'brands');
    }
}

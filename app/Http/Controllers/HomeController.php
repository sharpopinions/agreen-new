<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $langId = Language::currentId();

        $categories = Category::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->whereNull('parent_id')
            ->orderBy('sort_order')
            ->get()
            ->map(fn($cat) => [
                'id'   => $cat->id,
                'name' => $cat->translations->first()?->name ?? '',
                'slug' => $cat->translations->first()?->slug ?? '',
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

        $products = Product::with([
            'translations'        => fn($q) => $q->where('language_id', $langId),
            'badges.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->where('store_id', 1)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id,
                'name'     => $p->translations->first()?->name ?? '',
                'slug'     => $p->translations->first()?->slug ?? '',
                'sku'      => $p->sku,
                'price'    => (float) $p->price,
                'oldPrice' => $p->old_price ? (float) $p->old_price : null,
                'badge'    => $p->badges->first() ? [
                    'name'    => $p->badges->first()->translations->first()?->name ?? '',
                    'color'   => $p->badges->first()->color,
                    'bgColor' => $p->badges->first()->bg_color,
                ] : null,
                'rating'   => 0,
                'reviews'  => 0,
            ]);

        return Inertia::render('Home', compact('categories', 'brands', 'products'));
    }
}

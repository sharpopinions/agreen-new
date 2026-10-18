<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Models\Product;
use App\Presenters\ProductCard;
use App\Support\Seo;
use App\Support\SiteSettings;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    public function index(): Response
    {
        $langId = Language::currentId();

        $categories = self::rootCategories($langId);

        $brands = Brand::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'name' => $b->translations->first()?->name ?? '',
                'slug' => $b->translations->first()?->slug ?? '',
            ]);

        $products = ProductCard::collection(
            Product::query()->forCard()->orderBy('sort_order')->get()
        );

        return Inertia::render('Home', [
            ...compact('categories', 'brands', 'products'),
            // Блок «Послуги» — перші 4 з адмінки
            'services' => \App\Models\Service::with(['translations' => fn($q) => $q->where('language_id', $langId)])
                ->where('is_active', true)->orderBy('sort_order')->limit(4)->get()
                ->map(fn($s, $i) => [
                    'n'     => str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT),
                    'title' => $s->translations->first()?->title ?? '',
                    'href'  => '/services/' . $s->translations->first()?->slug,
                    'desc'  => \App\Support\Seo::excerpt($s->translations->first()?->summary, 90),
                ]),
            // Блок «Блог та новини» — 4 останні записи
            'posts' => BlogController::query()->limit(4)->get()->map(fn($p) => BlogController::card($p)),
            'seo' => Seo::make(
                'A-green — матеріали для кузовного ремонту та промисловості',
                SiteSettings::forFrontend()['footerText'],
                raw: true,
            ),
        ]);
    }

    /** Кореневі категорії з підкатегоріями (головна, кабінет → Каталог). */
    public static function rootCategories(int $langId)
    {
        return Category::with([
            'translations'          => fn($q) => $q->where('language_id', $langId),
            'children.translations' => fn($q) => $q->where('language_id', $langId),
        ])
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
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
            ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersProductListing;
use App\Models\Brand;
use App\Models\BrandMedia;
use App\Models\Category;
use App\Models\Language;
use App\Support\Html;
use App\Support\CurrentStore;
use App\Support\Seo;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/** Бренди (Claude Design → BrandsPage, BrandDetailPage, BrandProductsPage). */
class BrandController extends Controller
{
    use RendersProductListing;

    public function index(): Response
    {
        $langId = Language::currentId();

        $brands = Brand::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        // Кореневі категорії, в яких є активні товари кожного бренду — одним запитом
        $brandRoots = DB::table('products')
            ->join('category_product', 'category_product.product_id', '=', 'products.id')
            ->join('categories', 'categories.id', '=', 'category_product.category_id')
            ->where('products.is_active', true)
            ->where('products.store_id', CurrentStore::id())
            ->whereNotNull('products.brand_id')
            ->selectRaw('products.brand_id, COALESCE(categories.parent_id, categories.id) as root_id')
            ->distinct()
            ->get()
            ->groupBy('brand_id')
            ->map(fn($rows) => $rows->pluck('root_id')->map(fn($id) => (int) $id)->values());

        $rootIds = $brandRoots->flatten()->unique();
        $categories = Category::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->whereIn('id', $rootIds)->where('is_active', true)->orderBy('sort_order')->get();

        return Inertia::render('Brands', [
            'brands' => $brands->map(fn(Brand $b) => [
                'id'         => $b->id,
                'name'       => $b->translations->first()?->name ?? '',
                'slug'       => $b->translations->first()?->slug ?? '',
                'logo'       => $b->logo_url,
                'count'      => $b->products_count,
                'categories' => $brandRoots[$b->id] ?? [],
            ])->filter(fn($b) => $b['slug'] !== '')->values(),
            'categories' => $categories->map(fn($c) => [
                'id'   => $c->id,
                'name' => $c->translations->first()?->name ?? '',
            ])->values(),
            'seo' => Seo::make('Бренди', 'Ми представляємо провідних виробників у своїх категоріях'),
        ]);
    }

    /** Вкладка «Продукція»: товари бренду з фільтрами. */
    public function show(Request $request, string $slug): Response
    {
        $brand = $this->findBrand($slug);

        return $this->listing($request, Language::currentId(), null, 'Brand', $brand, $this->brandProps($brand, 'products', [
            'noindex' => $request->query->count() > 0,
        ]));
    }

    public function about(string $slug): Response
    {
        $brand = $this->findBrand($slug);
        $langId = Language::currentId();

        return Inertia::render('Brand', $this->brandProps($brand, 'about') + [
            'about'       => Html::clean($brand->translations->first()?->description) ?: null,
            'otherBrands' => Brand::with(['translations' => fn($q) => $q->where('language_id', $langId)])
                ->where('is_active', true)->whereKeyNot($brand->id)->orderBy('sort_order')->limit(12)->get()
                ->map(fn(Brand $b) => ['name' => $b->translations->first()?->name ?? '', 'slug' => $b->translations->first()?->slug ?? ''])
                ->filter(fn($b) => $b['slug'] !== '')->values(),
        ]);
    }

    public function media(string $slug): Response
    {
        $brand = $this->findBrand($slug);
        abort_if($brand->media->isEmpty(), 404);

        $item = fn(BrandMedia $m) => [
            'id'    => $m->id,
            'title' => $m->title,
            'url'   => $m->url,
            'ext'   => strtoupper(pathinfo($m->path, PATHINFO_EXTENSION)),
            'size'  => $m->type !== 'video' ? $this->fileSize($m->path) : null,
            'date'  => $m->updated_at?->translatedFormat('d M Y'),
        ];

        return Inertia::render('Brand', $this->brandProps($brand, 'media') + [
            'media' => [
                'videos'    => $brand->media->where('type', 'video')->map(fn($m) => ['id' => $m->path, 'title' => $m->title])->values(),
                'images'    => $brand->media->where('type', 'image')->map($item)->values(),
                'documents' => $brand->media->where('type', 'pdf')->map($item)->values(),
            ],
        ]);
    }

    private function findBrand(string $slug): Brand
    {
        $langId = Language::currentId();

        return Brand::with(['translations' => fn($q) => $q->where('language_id', $langId), 'media'])
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
            ->where('is_active', true)
            ->whereHas('translations', fn($q) => $q->where('slug', $slug)->where('language_id', $langId))
            ->firstOrFail();
    }

    /** Шапка бренду й вкладки — спільні для всіх вкладок. */
    private function brandProps(Brand $brand, string $tab, array $options = []): array
    {
        $t    = $brand->translations->first();
        $name = $t?->name ?? '';
        $titles = ['products' => "Продукція {$name}", 'about' => "{$name}: про бренд", 'media' => "{$name}: медіабанк"];

        return [
            'brand' => [
                'id'               => $brand->id,
                'name'             => $name,
                'slug'             => $t?->slug ?? '',
                'logo'             => $brand->logo_url,
                // Без короткого опису — початок «Про бренд», щоб шапка не була порожньою
                'shortDescription' => $t?->short_description ?: Seo::excerpt($t?->description, 220),
                'stats'            => array_values($t?->stats ?? []),
                'productsCount'    => $brand->products_count,
                'hasAbout'         => trim(strip_tags((string) $t?->description)) !== '',
                'hasMedia'         => $brand->media->isNotEmpty(),
            ],
            'tab' => $tab,
            'seo' => Seo::make(
                $tab === 'products' && $t?->meta_title ? $t->meta_title : $titles[$tab],
                ($tab === 'products' ? $t?->meta_description : null) ?: ($t?->short_description ?: $t?->description),
                $brand->logo_url,
                noindex: $options['noindex'] ?? false,
                raw: $tab === 'products' && (bool) $t?->meta_title,
            ),
        ];
    }

    private function fileSize(string $path): ?string
    {
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        if (! $disk->exists($path)) {
            return null;
        }
        $bytes = $disk->size($path);

        return $bytes >= 1048576 ? round($bytes / 1048576, 1) . ' MB' : max(1, round($bytes / 1024)) . ' KB';
    }
}

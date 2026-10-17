<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\RendersProductListing;
use App\Models\Language;
use App\Models\Promotion;
use App\Support\Html;
use App\Support\Seo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/** Акції (Claude Design → AktsyiiPage, AktsyiaPage). */
class PromotionController extends Controller
{
    use RendersProductListing;

    public function index(): Response
    {
        $promotions = $this->query()->orderBy('sort_order')->orderBy('ends_at')->get()
            ->map(fn(Promotion $p) => $this->card($p))
            ->filter(fn($p) => $p['slug'] !== '')
            ->values();

        return Inertia::render('Promotions', [
            'promotions' => $promotions,
            'seo'        => Seo::make('Акції', 'Спеціальні пропозиції та знижки для наших клієнтів'),
        ]);
    }

    public function show(Request $request, string $slug): Response
    {
        $langId    = Language::currentId();
        $promotion = $this->query()
            ->whereHas('translations', fn($q) => $q->where('slug', $slug)->where('language_id', $langId))
            ->firstOrFail();
        $t = $promotion->translations->first();

        return $this->listing($request, $langId, null, 'Promotion', extra: [
            'promotion' => $this->card($promotion) + [
                'description' => Html::clean($t?->description) ?: null,
                'productsCount' => $promotion->constrainProducts(\App\Models\Product::query()->where('is_active', true))->count(),
            ],
            'seo' => Seo::make(
                $t?->meta_title ?: ($t?->title ?? ''),
                $t?->meta_description ?: $t?->description,
                $promotion->image ? Storage::disk('public')->url($promotion->image) : null,
                noindex: $request->query->count() > 0,
                raw: (bool) $t?->meta_title,
            ),
        ], scope: fn($q) => $promotion->constrainProducts($q));
    }

    private function query()
    {
        $langId = Language::currentId();
        $tr     = fn($q) => $q->where('language_id', $langId);

        return Promotion::running()->with(['translations' => $tr, 'brand.translations' => $tr, 'category.translations' => $tr]);
    }

    private function card(Promotion $p): array
    {
        $t = $p->translations->first();

        return [
            'id'       => $p->id,
            'title'    => $t?->title ?? '',
            'slug'     => $t?->slug ?? '',
            'discount' => $p->discount_percent,
            'until'    => $p->ends_at?->translatedFormat('j F Y'),
            'image'    => $p->image ? Storage::disk('public')->url($p->image) : null,
            'brand'    => $p->brand ? [
                'id'   => $p->brand->id,
                'name' => $p->brand->translations->first()?->name ?? '',
                'slug' => $p->brand->translations->first()?->slug ?? '',
                'logo' => $p->brand->logo_url,
            ] : null,
            'category' => $p->category ? [
                'id'   => $p->category->id,
                'name' => $p->category->translations->first()?->name ?? '',
            ] : null,
        ];
    }
}

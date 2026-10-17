<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Language;
use App\Models\PageTranslation;
use App\Support\Html;
use App\Support\Seo;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/** Інформаційні сторінки з блоками (адмінка → Контент → Сторінки). */
class PageController extends Controller
{
    public function show(string $slug): Response
    {
        $translation = PageTranslation::with('page')
            ->where('slug', $slug)
            ->where('language_id', Language::currentId())
            ->whereHas('page', fn($q) => $q->where('is_active', true))
            ->firstOrFail();

        $page   = $translation->page;
        $blocks = collect($translation->blocks ?? [])->values()
            ->map(fn($b) => $this->block($b['type'] ?? '', $b['data'] ?? []))
            ->filter()->values();

        $firstText = $blocks->first(fn($b) => ! empty($b['html']))['html'] ?? null;

        return Inertia::render('Page', [
            'page' => [
                'title'  => $translation->title,
                'layout' => $page->layout,
                'blocks' => $blocks,
                // Зміст для «документа» — заголовки розділів
                'toc'    => $page->layout === 'document'
                    ? $blocks->where('type', 'section')->pluck('title')->values()
                    : [],
            ],
            'seo' => Seo::make(
                $translation->meta_title ?: $translation->title,
                $translation->meta_description ?: $firstText,
                raw: (bool) $translation->meta_title,
            ),
        ]);
    }

    /** Дані блока для фронтенду: HTML очищено, файли — URL, «бренди» — з бази. */
    private function block(string $type, array $data): ?array
    {
        $clean = fn(?string $html) => Html::clean($html);

        return match ($type) {
            'section'     => ['type' => $type, 'title' => $data['title'] ?? '', 'html' => $clean($data['html'] ?? '')],
            'text'        => ['type' => $type, 'html' => $clean($data['html'] ?? '')],
            'image'       => ['type' => $type, 'url' => ! empty($data['path']) ? Storage::disk('public')->url($data['path']) : null, 'alt' => $data['alt'] ?? ''],
            'intro_stats' => ['type' => $type, 'heading' => $data['heading'] ?? '', 'html' => $clean($data['html'] ?? ''), 'stats' => array_values($data['stats'] ?? [])],
            'features'    => ['type' => $type, 'heading' => $data['heading'] ?? '', 'items' => array_values($data['items'] ?? [])],
            'columns'     => ['type' => $type, 'columns' => collect($data['columns'] ?? [])->values()
                ->map(fn($c) => ['heading' => $c['heading'] ?? '', 'items' => array_values($c['items'] ?? [])])->all()],
            'brands'      => ['type' => $type, 'heading' => $data['heading'] ?? '', 'brands' => $this->brands()],
            'cta'         => ['type' => $type, 'heading' => $data['heading'] ?? '', 'text' => $data['text'] ?? '', 'buttons' => array_values($data['buttons'] ?? [])],
            default       => null,
        };
    }

    private function brands(): array
    {
        $langId = Language::currentId();

        return Brand::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('is_active', true)->orderBy('sort_order')->get()
            ->map(fn(Brand $b) => [
                'id'   => $b->id,
                'name' => $b->translations->first()?->name ?? '',
                'slug' => $b->translations->first()?->slug ?? '',
                'logo' => $b->logo ? Storage::disk('public')->url($b->logo) : null,
            ])->all();
    }
}

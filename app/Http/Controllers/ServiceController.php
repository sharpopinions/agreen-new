<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Service;
use App\Models\Testimonial;
use App\Support\Html;
use App\Support\Seo;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

/** Послуги (Claude Design → ServicesPage, StudyCenterPage). */
class ServiceController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Services', [
            'services'     => $this->query()->get()->map(fn(Service $s) => $this->card($s))->filter(fn($s) => $s['slug'])->values(),
            'testimonials' => Testimonial::where('is_active', true)->orderBy('sort_order')->limit(6)->get(['id', 'author', 'text', 'rating']),
            'seo'          => Seo::make('Послуги', 'Повний спектр послуг для підтримки вашого бізнесу — від навчання персоналу до технічного обслуговування обладнання.'),
        ]);
    }

    public function show(string $slug): Response
    {
        $langId  = Language::currentId();
        $service = $this->query()
            ->whereHas('translations', fn($q) => $q->where('slug', $slug)->where('language_id', $langId))
            ->firstOrFail();
        $t = $service->translations->first();

        return Inertia::render('Service', [
            'service' => $this->card($service) + [
                'body'     => Html::clean($t?->body) ?: null,
                'stats'    => array_values($t?->stats ?? []),
                'benefits' => array_values($t?->benefits ?? []),
                'courses'  => array_values($t?->courses ?? []),
            ],
            'seo' => Seo::make(
                $t?->meta_title ?: ($t?->title ?? ''),
                $t?->meta_description ?: $t?->summary,
                $service->image ? Storage::disk('public')->url($service->image) : null,
                raw: (bool) $t?->meta_title,
            ),
        ]);
    }

    private function query()
    {
        $langId = Language::currentId();

        return Service::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('is_active', true)
            ->orderBy('sort_order');
    }

    private function card(Service $s): array
    {
        $t = $s->translations->first();

        return [
            'id'      => $s->id,
            'icon'    => $s->icon,
            'image'   => $s->image ? Storage::disk('public')->url($s->image) : null,
            'title'   => $t?->title ?? '',
            'slug'    => $t?->slug ?? '',
            'summary' => $t?->summary,
            'details' => collect($t?->details ?? [])->map(fn($d) => is_array($d) ? ($d['text'] ?? '') : $d)->filter()->values(),
        ];
    }
}

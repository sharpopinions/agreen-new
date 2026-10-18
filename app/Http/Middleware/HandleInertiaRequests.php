<?php

namespace App\Http\Middleware;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Language;
use App\Services\Cart;
use App\Support\SiteSettings;
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
            // Поточний покупець (хедер, кабінет, оформлення замовлення)
            'auth'  => fn () => ($u = $request->user()) ? ['user' => [
                'id'            => $u->id,
                'name'          => $u->name,
                'lastName'      => $u->last_name,
                'email'         => $u->email,
                'phone'         => $u->phone,
                'company'       => $u->company,
                'role'          => $u->role,
                'requestedRole' => $u->requested_role,
                'verified'      => $u->hasVerifiedEmail(),
                'isStaff'       => $u->isStaff(),
                'isBusiness'    => $u->isBusiness(),
            ]] : ['user' => null],
            'flash' => fn () => [
                'cartAdded' => $request->session()->get('cartAdded'),
                'leadSent'  => $request->session()->get('leadSent'),
                'notice'    => $request->session()->get('notice'),
                'auth'      => $request->session()->get('auth'),
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
            'nav'  => fn () => $this->navigation(),
            // Контакти, графік, соцмережі (адмінка → Налаштування → Сайт і контакти)
            'site' => fn () => SiteSettings::forFrontend(),
        ];
    }

    private function navigation(): array
    {
        $langId = Language::currentId();

        $categories = Category::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->withCount(['products' => fn($q) => $q->where('is_active', true)])
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
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get()
            ->map(fn($b) => [
                'id'   => $b->id,
                'name' => $b->translations->first()?->name ?? '',
                'slug' => $b->translations->first()?->slug ?? '',
            ]);

        // Послуги для мегаменю й футера (адмінка → Контент → Послуги)
        $services = \App\Models\Service::with(['translations' => fn($q) => $q->where('language_id', $langId)])
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->limit(8)
            ->get()
            ->map(fn($s) => [
                'icon'  => $s->icon,
                'title' => $s->translations->first()?->title ?? '',
                'slug'  => $s->translations->first()?->slug ?? '',
                'desc'  => \App\Support\Seo::excerpt($s->translations->first()?->summary, 40),
            ])
            ->filter(fn($s) => $s['slug'] !== '')
            ->values();

        return compact('categories', 'brands', 'services');
    }
}

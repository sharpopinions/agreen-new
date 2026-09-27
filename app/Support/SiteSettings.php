<?php

namespace App\Support;

use App\Models\Store;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;

/**
 * Контакти й загальні дані сайту (хедер, футер, мегаменю, сторінка контактів).
 * Зберігаються в stores.settings['site'], редагуються в адмінці «Налаштування → Сайт».
 * Поля з перекладами — масиви ['uk' => …, 'en' => …]; на сайт іде значення поточної мови.
 */
class SiteSettings
{
    /** Поля, що мають переклади. */
    public const TRANSLATABLE = ['address', 'schedule_short', 'schedule', 'footer_text'];

    public const SOCIALS = ['instagram', 'facebook', 'telegram', 'viber', 'youtube', 'tiktok', 'linkedin'];

    /** Початкові значення — з дизайну (сторінка «Контакти»). */
    public const DEFAULTS = [
        'phone'  => '+38 (097) 075-71-70',
        'emails' => ['info@a-green.com.ua', 'office@a-green.com.ua'],
        'departments' => [
            ['name' => ['uk' => 'Відділ продажу', 'en' => 'Sales', 'pl' => 'Dział sprzedaży'],
             'phones' => ['+38 (097) 075-71-70', '+38 (050) 075-71-70', '+38 (063) 075-71-70']],
            ['name' => ['uk' => 'Бухгалтерія', 'en' => 'Accounting', 'pl' => 'Księgowość'],
             'phones' => ['+38 (097) 075-71-70']],
        ],
        'address'        => ['uk' => '02660, м. Київ, вул. Крайня, 1', 'en' => '1 Krainia St., Kyiv, 02660', 'pl' => 'ul. Krajnia 1, Kijów, 02660'],
        'schedule_short' => ['uk' => 'Пн–Чт 9:00–16:00, Пт до 15:00', 'en' => 'Mon–Thu 9:00–16:00, Fri until 15:00', 'pl' => 'Pn–Czw 9:00–16:00, Pt do 15:00'],
        'schedule'       => ['uk' => "Пн – Чт: 9:00 – 16:00\nПт: до 15:00\nСб та Нд: вихідні", 'en' => "Mon – Thu: 9:00 – 16:00\nFri: until 15:00\nSat & Sun: closed", 'pl' => "Pn – Czw: 9:00 – 16:00\nPt: do 15:00\nSob i Nd: nieczynne"],
        'footer_text'    => ['uk' => 'Широкий асортимент продукції для промислових підприємств та кузовного ремонту.', 'en' => 'A wide range of products for industrial enterprises and body repair.', 'pl' => 'Szeroki asortyment produktów dla przedsiębiorstw przemysłowych i napraw blacharskich.'],
        'socials'        => [],
        'map_url'        => null,
    ];

    private static function cacheKey(): string
    {
        return 'site_settings.' . CurrentStore::id();
    }

    /** Усі налаштування (сирі, з усіма мовами). */
    public static function all(): array
    {
        return Cache::rememberForever(self::cacheKey(), function () {
            $saved = Store::find(CurrentStore::id())?->settings['site'] ?? [];

            return array_replace(self::DEFAULTS, $saved);
        });
    }

    public static function save(array $site): void
    {
        $store = Store::findOrFail(CurrentStore::id());
        $store->settings = array_replace($store->settings ?? [], ['site' => $site]);
        $store->save();

        Cache::forget(self::cacheKey());
    }

    /** Дані для фронтенду поточною мовою. */
    public static function forFrontend(?string $locale = null): array
    {
        $locale = $locale ?? app()->getLocale();
        $s = self::all();
        $t = fn($value) => is_array($value) ? ($value[$locale] ?? $value['uk'] ?? Arr::first($value) ?? '') : (string) $value;

        return [
            'phone'         => self::phone($s['phone']),
            'emails'        => array_values(array_filter($s['emails'] ?? [])),
            'departments'   => collect($s['departments'] ?? [])->map(fn($d) => [
                'name'   => $t($d['name'] ?? ''),
                'phones' => collect($d['phones'] ?? [])->filter()->map(fn($p) => self::phone($p))->values()->all(),
            ])->filter(fn($d) => $d['phones'])->values()->all(),
            'address'       => $t($s['address']),
            'scheduleShort' => $t($s['schedule_short']),
            'schedule'      => $t($s['schedule']),
            'footerText'    => $t($s['footer_text']),
            'socials'       => collect(self::SOCIALS)
                ->filter(fn($k) => ! empty($s['socials'][$k]))
                ->map(fn($k) => ['key' => $k, 'url' => $s['socials'][$k]])
                ->values()->all(),
            'mapUrl'        => $s['map_url'] ?: null,
        ];
    }

    /** Номер для показу + посилання tel: (лише цифри й +). */
    private static function phone(?string $number): ?array
    {
        if (! $number) {
            return null;
        }
        $digits = preg_replace('/[^\d+]/', '', $number);

        return ['label' => $number, 'href' => 'tel:' . $digits];
    }
}

<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Мета-теги сторінки (prop `seo`): title, description, og:image, noindex.
 * Рендеряться і сервером (app.blade.php — для пошукових систем), і клієнтом (SeoHead.vue — при переходах).
 */
class Seo
{
    public const SITE = 'A-green';

    public static function make(string $title, ?string $description = null, ?string $image = null, bool $noindex = false, bool $raw = false): array
    {
        $site = self::SITE;

        return [
            'title'       => $raw || str_contains($title, $site) ? $title : "{$title} — {$site}",
            'description' => self::excerpt($description),
            'image'       => $image ? url($image) : null,
            'noindex'     => $noindex,
        ];
    }

    /** Текст без HTML, стиснутий до ~160 символів. */
    public static function excerpt(?string $text, int $limit = 160): ?string
    {
        $plain = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags((string) $text))));

        return $plain === '' ? null : Str::limit($plain, $limit);
    }
}

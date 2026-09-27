<?php

namespace App\Services;

use App\Models\UiTranslation;
use Illuminate\Support\Facades\Cache;

class DatabaseTranslationLoader
{
    public function loadIntoTranslator(): void
    {
        try {
            $groups = Cache::remember('ui_translations.' . \App\Support\CurrentStore::id(), 300, function () {
                return UiTranslation::with('values')
                    ->get()
                    ->groupBy('group');
            });

            foreach ($groups as $group => $translations) {
                foreach (['uk', 'en', 'pl'] as $locale) {
                    $lines = [];
                    foreach ($translations as $translation) {
                        $value = $translation->values
                            ->where('locale', $locale)
                            ->first()
                            ?->value;
                        if ($value !== null && $value !== '') {
                            $lines[$translation->key] = $value;
                        }
                    }
                    if (!empty($lines)) {
                        app('translator')->addLines($lines, $locale, $group ?: '*');
                    }
                }
            }
        } catch (\Exception) {
            // DB not ready yet (e.g., during migrations)
        }
    }

    public static function clearCache(): void
    {
        Cache::forget('ui_translations');
    }
}

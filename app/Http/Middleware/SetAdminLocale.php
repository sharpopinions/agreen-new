<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetAdminLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        // Пакет bezhansalleh/filament-language-switch зберігає locale в сесії
        $locale = session('locale');

        // Якщо сесія порожня — читаємо з профілю користувача
        if (!$locale && auth()->check()) {
            $locale = auth()->user()->locale;
        }

        if ($locale) {
            app()->setLocale($locale);

            // Синхронізуємо в БД, щоб наступна сесія запам'ятала вибір
            if (auth()->check() && auth()->user()->locale !== $locale) {
                auth()->user()->updateQuietly(['locale' => $locale]);
            }
        }

        return $next($request);
    }
}

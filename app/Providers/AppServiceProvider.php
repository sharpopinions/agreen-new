<?php

namespace App\Providers;

use App\Models\Language;
use App\Observers\LanguageObserver;
use App\Services\DatabaseTranslationLoader;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Language::observe(LanguageObserver::class);

        // Codespaces/проксі: брати домен і схему з X-Forwarded-* (див. config/app.php)
        if ($proxies = config('app.trusted_proxies')) {
            TrustProxies::at($proxies === '*' ? '*' : array_map('trim', explode(',', $proxies)));
        }

        $this->app->booted(function () {
            app(DatabaseTranslationLoader::class)->loadIntoTranslator();
        });
    }
}

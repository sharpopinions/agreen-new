<?php

namespace App\Providers;

use App\Models\Language;
use App\Observers\LanguageObserver;
use App\Services\DatabaseTranslationLoader;
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

        $this->app->booted(function () {
            app(DatabaseTranslationLoader::class)->loadIntoTranslator();
        });
    }
}

<?php

namespace App\Providers;

use App\Models\Language;
use App\Observers\LanguageObserver;
use App\Services\DatabaseTranslationLoader;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Http\Middleware\TrustProxies;
use Illuminate\Notifications\Messages\MailMessage;
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

        // Лист «Скидання пароля» українською
        ResetPassword::toMailUsing(fn($user, string $token) => (new MailMessage)
            ->subject('Відновлення пароля A-green')
            ->greeting('Вітаємо!')
            ->line('Ви (або хтось інший) запросили зміну пароля для входу на A-green.')
            ->action('Встановити новий пароль', url(route('password.reset', ['token' => $token, 'email' => $user->getEmailForPasswordReset()], false)))
            ->line('Посилання діє 60 хвилин. Якщо ви не запитували зміну пароля — нічого не робіть.'));

        $this->app->booted(function () {
            app(DatabaseTranslationLoader::class)->loadIntoTranslator();
        });
    }
}

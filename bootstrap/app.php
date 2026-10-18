<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Support\Seo;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Уже авторизований відкриває /login чи реєстрацію — у кабінет
        $middleware->redirectUsersTo('/account');

        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        // 404 на сайті — сторінка з дизайну (Page404), а не стандартна Laravel.
        // Адмінка, Livewire і JSON-запити лишаються як є.
        $exceptions->respond(function (Response $response, Throwable $e, Request $request) {
            if ($response->getStatusCode() !== 404
                || $request->is('admin', 'admin/*', 'livewire/*', 'api/*', 'storage/*', 'build/*')
                || $request->expectsJson() && ! $request->header('X-Inertia')) {
                return $response;
            }

            return Inertia::render('NotFound', ['seo' => Seo::make('Сторінку не знайдено', noindex: true)])
                ->toResponse($request)
                ->setStatusCode(404);
        });
    })->create();

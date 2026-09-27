<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php($seo = $page['props']['seo'] ?? null)
    {{-- Сервером — для пошукових систем; далі мета-теги оновлює SeoHead.vue --}}
    <title inertia>{{ $seo['title'] ?? \App\Support\Seo::SITE }}</title>
    @if ($seo)
        @if ($seo['description'])<meta name="description" content="{{ $seo['description'] }}" inertia="description">@endif
        <meta property="og:title" content="{{ $seo['title'] }}" inertia="og:title">
        @if ($seo['description'])<meta property="og:description" content="{{ $seo['description'] }}" inertia="og:description">@endif
        @if ($seo['image'])<meta property="og:image" content="{{ $seo['image'] }}" inertia="og:image">@endif
        @if ($seo['noindex'])<meta name="robots" content="noindex, nofollow" inertia="robots">@endif
    @endif
    @vite(['resources/js/app.js'])
    @inertiaHead
</head>
<body>
    @inertia
</body>
</html>

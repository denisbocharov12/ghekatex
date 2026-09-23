@php
    $isAdmin = request()->is('admin', 'admin/*');
    $locale = app()->getLocale();
@endphp
<!DOCTYPE html>
<html lang="{{ config("ghekatex.locales.html.{$locale}", $locale) }}" class="{{ $isAdmin ? 'admin' : 'site' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="icon" type="image/svg+xml" href="/brand/icon_pattern.svg">
    <link rel="apple-touch-icon" href="/brand/icon_pattern.svg">
    <meta name="theme-color" content="#0b2239">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    @if ($isAdmin)
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @else
        {{-- Узкое начертание для заголовков, обычное — для текста --}}
        <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Condensed:wght@500;600;700&family=IBM+Plex+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @endif

    @unless ($isAdmin)
        @include('partials.seo')
    @endunless

    @routes
    @vite([$isAdmin ? 'resources/js/admin/app.ts' : 'resources/js/site/app.ts'])
    @inertiaHead
</head>
<body class="antialiased">
    @inertia
</body>
</html>

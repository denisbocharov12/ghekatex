@php
    /*
    | Мета-теги в ответе сервера.
    |
    | Inertia рисует их на клиенте, но краулеры соцсетей и часть поисковых
    | роботов JavaScript не выполняют и видели пустой документ. Здесь те же
    | значения берутся из пропса `seo` и печатаются сразу. Атрибут
    | `data-server-seo` помечает их как серверные: смонтировавшись, SeoHead
    | снимает эти теги и дальше ведёт разметку сам — дублей не остаётся.
    */
    $seo = $page['props']['seo'] ?? null;
@endphp

@if (is_array($seo))
    <title>{{ $seo['title'] ?? '' }}</title>

    @if (! empty($seo['description']))
        <meta data-server-seo name="description" content="{{ $seo['description'] }}">
    @endif

    @if (! empty($seo['keywords']))
        <meta data-server-seo name="keywords" content="{{ $seo['keywords'] }}">
    @endif

    <meta data-server-seo name="robots" content="{{ $seo['robots'] ?? 'index,follow' }}">

    @if (! empty($seo['canonical']))
        <link data-server-seo rel="canonical" href="{{ $seo['canonical'] }}">
    @endif

    @foreach (($seo['alternates'] ?? []) as $hreflang => $href)
        <link data-server-seo rel="alternate" hreflang="{{ $hreflang }}" href="{{ $href }}">
    @endforeach

    <meta data-server-seo property="og:type" content="website">
    <meta data-server-seo property="og:site_name" content="GHEKATEX">
    <meta data-server-seo property="og:title" content="{{ $seo['og_title'] ?? ($seo['title'] ?? '') }}">

    @if (! empty($seo['og_description']))
        <meta data-server-seo property="og:description" content="{{ $seo['og_description'] }}">
    @endif

    @if (! empty($seo['og_image']))
        <meta data-server-seo property="og:image" content="{{ $seo['og_image'] }}">
    @endif

    @if (! empty($seo['canonical']))
        <meta data-server-seo property="og:url" content="{{ $seo['canonical'] }}">
    @endif

    <meta data-server-seo name="twitter:card" content="summary_large_image">
    <meta data-server-seo name="twitter:title" content="{{ $seo['og_title'] ?? ($seo['title'] ?? '') }}">

    @if (! empty($seo['og_description']))
        <meta data-server-seo name="twitter:description" content="{{ $seo['og_description'] }}">
    @endif

    @if (! empty($seo['og_image']))
        <meta data-server-seo name="twitter:image" content="{{ $seo['og_image'] }}">
    @endif

    @if (! empty($seo['schema']))
        <script data-server-seo type="application/ld+json">{!! json_encode(count($seo['schema']) === 1 ? $seo['schema'][0] : $seo['schema'], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endif
@endif

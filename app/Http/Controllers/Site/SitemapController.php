<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\SitemapBuilder;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index(SitemapBuilder $builder): Response
    {
        // Карта пересобирается раз в час: обход поисковиком чаще не бывает
        $xml = Cache::remember('ghekatex.sitemap', now()->addHour(), fn () => $builder->build()->render());

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /api',
            '',
            'Sitemap: '.route('sitemap'),
            '',
        ];

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }
}

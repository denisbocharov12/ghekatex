<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Services\SeoService;

/**
 * Общая часть страниц витрины: хлебные крошки и доступ к SEO-сервису.
 */
abstract class SiteController extends Controller
{
    public function __construct(
        protected readonly SeoService $seo,
    ) {}

    /**
     * Хлебные крошки. Первым элементом всегда идёт главная.
     *
     * @param  array<int, array{label: string, url?: string|null}>  $items
     * @return array<int, array{label: string, url: string|null}>
     */
    protected function crumbs(array $items): array
    {
        $crumbs = [[
            'label' => __('Главная'),
            'url' => route('home', ['locale' => app()->getLocale()]),
        ]];

        foreach ($items as $item) {
            $crumbs[] = [
                'label' => $item['label'],
                'url' => $item['url'] ?? null,
            ];
        }

        return $crumbs;
    }
}

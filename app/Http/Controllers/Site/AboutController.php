<?php

namespace App\Http\Controllers\Site;

use App\Models\Page;
use App\Services\AboutPageService;
use App\Services\SeoService;
use Inertia\Inertia;
use Inertia\Response;

class AboutController extends SiteController
{
    public function __construct(
        SeoService $seo,
        private readonly AboutPageService $page,
    ) {
        parent::__construct($seo);
    }

    public function __invoke(string $locale): Response
    {
        // Вступление редактируется как обычная страница — так редактор правит
        // тексты и SEO в одном месте, не трогая справочники
        $page = Page::query()->with('seo')->where('slug', 'about')->first();
        $title = $page?->getTranslation('title', $locale, true) ?: __('О компании');

        $crumbs = $this->crumbs([['label' => $title]]);

        return Inertia::render('About', array_merge($this->page->build(), [
            'page' => $page === null ? null : [
                'title' => $title,
                'subtitle' => $page->getTranslation('subtitle', $locale, true),
                'body' => $page->getTranslation('body', $locale, true),
                'cover' => $page->getFirstMediaUrl('cover', 'wide') ?: null,
            ],
            'breadcrumbs' => $crumbs,
            'seo' => ($page !== null
                ? $this->seo->forModel($page, $title, breadcrumbs: $crumbs)
                : $this->seo->forPage($title, breadcrumbs: $crumbs)
            )->toArray(),
        ]));
    }
}

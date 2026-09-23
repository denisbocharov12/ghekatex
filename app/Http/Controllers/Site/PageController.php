<?php

namespace App\Http\Controllers\Site;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends SiteController
{
    public function show(string $locale, Page $page): Response
    {
        abort_unless($page->is_active, 404);

        // Раздел «О компании» имеет собственный маршрут с историей и мощностями
        abort_if($page->slug === 'about', 404);

        $page->load('seo');
        $current = app()->getLocale();
        $title = (string) $page->getTranslation('title', $current, true);
        $crumbs = $this->crumbs([['label' => $title]]);

        return Inertia::render('Page', [
            'page' => [
                'slug' => $page->slug,
                'template' => $page->template->value,
                'title' => $title,
                'subtitle' => $page->getTranslation('subtitle', $current, true),
                'body' => $page->getTranslation('body', $current, true),
                'blocks' => $page->blocks ?? [],
                'cover' => $page->getFirstMediaUrl('cover', 'wide') ?: null,
                'updated_at' => $page->updated_at?->toIso8601String(),
            ],
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forModel($page, $title, breadcrumbs: $crumbs)->toArray(),
        ]);
    }
}

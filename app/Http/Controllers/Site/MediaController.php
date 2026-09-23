<?php

namespace App\Http\Controllers\Site;

use App\Repositories\Contracts\MediaAlbumRepositoryInterface;
use App\Services\SeoService;
use App\Support\Presenters\SitePresenter;
use Inertia\Inertia;
use Inertia\Response;

class MediaController extends SiteController
{
    public function __construct(
        SeoService $seo,
        private readonly MediaAlbumRepositoryInterface $albums,
    ) {
        parent::__construct($seo);
    }

    public function index(string $locale): Response
    {
        $title = __('Медиа-галерея');
        $crumbs = $this->crumbs([['label' => $title]]);

        return Inertia::render('Media/Index', [
            'albums' => SitePresenter::collect($this->albums->activeWithPreview(), fn ($item) => SitePresenter::album($item)),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forPage($title, breadcrumbs: $crumbs)->toArray(),
        ]);
    }

    public function show(string $locale, string $album): Response
    {
        $model = $this->albums->findActiveBySlugWithItems($album);
        $title = (string) $model->getTranslation('title', app()->getLocale(), true);

        $crumbs = $this->crumbs([
            ['label' => __('Медиа-галерея'), 'url' => route('media.index', ['locale' => $locale])],
            ['label' => $title],
        ]);

        return Inertia::render('Media/Show', [
            'album' => SitePresenter::album($model, withItems: true),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forModel($model, $title, breadcrumbs: $crumbs)->toArray(),
        ]);
    }
}

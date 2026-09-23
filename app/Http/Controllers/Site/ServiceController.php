<?php

namespace App\Http\Controllers\Site;

use App\Models\Service;
use App\Repositories\Contracts\FaqRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Services\SeoService;
use App\Support\Presenters\SitePresenter;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\SchemaOrg\Schema;

class ServiceController extends SiteController
{
    public function __construct(
        SeoService $seo,
        private readonly ServiceRepositoryInterface $services,
        private readonly FaqRepositoryInterface $faqs,
    ) {
        parent::__construct($seo);
    }

    public function index(string $locale): Response
    {
        $title = __('Услуги');
        $crumbs = $this->crumbs([['label' => $title]]);

        return Inertia::render('Services/Index', [
            'services' => SitePresenter::collect($this->services->activeOrdered(), fn ($item) => SitePresenter::service($item, full: true)),
            'faqs' => SitePresenter::collect($this->faqs->activeInGroup('services'), SitePresenter::faq(...)),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forPage($title, breadcrumbs: $crumbs)->toArray(),
        ]);
    }

    public function show(string $locale, Service $service): Response
    {
        abort_unless($service->is_active, 404);

        $service->load(['media', 'seo']);

        $name = (string) $service->getTranslation('name', $locale, true);
        $presented = SitePresenter::service($service, full: true);

        $crumbs = $this->crumbs([
            ['label' => __('Услуги'), 'url' => route('services.index', ['locale' => $locale])],
            ['label' => $name],
        ]);

        return Inertia::render('Services/Show', [
            'service' => $presented,
            'others' => SitePresenter::collect(
                $this->services->activeOrdered()->reject(fn ($item) => $item->is($service))->take(3),
                fn ($item) => SitePresenter::service($item),
            ),
            'breadcrumbs' => $crumbs,
            'seo' => $this->seo->forModel(
                model: $service,
                fallbackTitle: $name,
                fallbackDescription: $presented['summary'],
                fallbackImage: $presented['cover'],
                breadcrumbs: $crumbs,
                schema: [
                    Schema::service()
                        ->name($name)
                        ->description($presented['summary'])
                        ->areaServed('EU')
                        ->toArray(),
                ],
            )->toArray(),
        ]);
    }
}

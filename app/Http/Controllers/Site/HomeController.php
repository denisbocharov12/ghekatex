<?php

namespace App\Http\Controllers\Site;

use App\Services\HomePageService;
use App\Services\SeoService;
use App\Services\SettingService;
use Inertia\Inertia;
use Inertia\Response;

class HomeController extends SiteController
{
    public function __construct(
        SeoService $seo,
        private readonly HomePageService $page,
        private readonly SettingService $settings,
    ) {
        parent::__construct($seo);
    }

    public function __invoke(): Response
    {
        return Inertia::render('Home', array_merge($this->page->build(), [
            'seo' => $this->seo->forPage(
                title: $this->settings->get('seo_home_title', __('Производство женской одежды в Молдове')),
                description: $this->settings->get('seo_home_description'),
            )->toArray(),
        ]));
    }
}

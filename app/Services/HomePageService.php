<?php

namespace App\Services;

use App\Repositories\Contracts\AdvantageRepositoryInterface;
use App\Repositories\Contracts\FaqRepositoryInterface;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;
use App\Repositories\Contracts\OfficeRepositoryInterface;
use App\Repositories\Contracts\PartnerRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Support\Presenters\SitePresenter;

/**
 * Данные главной страницы. Все блоки необязательны: пустой раздел
 * просто не отрисовывается, поэтому сайт живёт и с частично заполненной базой.
 */
class HomePageService
{
    public function __construct(
        private readonly HeroSlideRepositoryInterface $slides,
        private readonly AdvantageRepositoryInterface $advantages,
        private readonly ProductCategoryRepositoryInterface $categories,
        private readonly ProductRepositoryInterface $products,
        private readonly ServiceRepositoryInterface $services,
        private readonly PostRepositoryInterface $posts,
        private readonly PartnerRepositoryInterface $partners,
        private readonly OfficeRepositoryInterface $offices,
        private readonly FaqRepositoryInterface $faqs,
        private readonly SettingService $settings,
    ) {}

    /** @return array<string, mixed> */
    public function build(): array
    {
        $home = $this->settings->group('home');

        return [
            'slides' => SitePresenter::collect($this->slides->activeOrdered(), SitePresenter::heroSlide(...)),
            'advantages' => SitePresenter::collect($this->advantages->activeOrdered(8), SitePresenter::advantage(...)),
            'categories' => SitePresenter::collect($this->categories->activeOrdered(6), fn ($item) => SitePresenter::productCategory($item)),
            'featured' => SitePresenter::collect(
                $this->products->featuredForHome(8),
                fn ($item) => SitePresenter::product($item),
            ),
            'services' => SitePresenter::collect($this->services->activeOrdered(4), fn ($item) => SitePresenter::service($item)),
            'posts' => SitePresenter::collect($this->posts->activeOrdered(3), fn ($item) => SitePresenter::post($item)),
            'partners' => SitePresenter::collect($this->partners->activeOrdered(12), SitePresenter::partner(...)),
            'faqs' => SitePresenter::collect($this->faqs->activeInGroup('general', 6), SitePresenter::faq(...)),
            'office' => SitePresenter::collect($this->offices->activeOrdered(1), SitePresenter::office(...))[0] ?? null,
            'intro' => [
                'eyebrow' => $home['home_intro_eyebrow'] ?? null,
                'title' => $home['home_intro_title'] ?? null,
                'text' => $home['home_intro_text'] ?? null,
                'video_url' => $home['home_video_url'] ?? null,
                'video_title' => $home['home_video_title'] ?? null,
            ],
        ];
    }
}

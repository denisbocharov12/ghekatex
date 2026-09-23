<?php

namespace App\Services;

use App\Repositories\Contracts\MediaAlbumRepositoryInterface;
use App\Repositories\Contracts\PageRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

/**
 * Карта сайта со всеми языковыми версиями каждого адреса.
 *
 * Альтернативы добавляются через `addAlternate`, иначе поисковик считает
 * переводы дублями и выбирает язык сам.
 */
class SitemapBuilder
{
    public function __construct(
        private readonly PageRepositoryInterface $pages,
        private readonly ProductCategoryRepositoryInterface $categories,
        private readonly ProductRepositoryInterface $products,
        private readonly ServiceRepositoryInterface $services,
        private readonly PostRepositoryInterface $posts,
        private readonly MediaAlbumRepositoryInterface $albums,
    ) {}

    public function build(): Sitemap
    {
        $sitemap = Sitemap::create();

        $this->addStatic($sitemap, 'home', 1.0, Url::CHANGE_FREQUENCY_WEEKLY);
        $this->addStatic($sitemap, 'about', 0.8);
        $this->addStatic($sitemap, 'catalog.index', 0.9, Url::CHANGE_FREQUENCY_WEEKLY);
        $this->addStatic($sitemap, 'services.index', 0.9);
        $this->addStatic($sitemap, 'news.index', 0.8, Url::CHANGE_FREQUENCY_DAILY);
        $this->addStatic($sitemap, 'media.index', 0.6);
        $this->addStatic($sitemap, 'contacts.index', 0.7);

        foreach ($this->categories->activeOrdered() as $category) {
            $this->addEntity($sitemap, 'catalog.category', ['category' => $category->slug], $category, 0.7);
        }

        foreach ($this->products->activeOrdered() as $product) {
            $this->addEntity($sitemap, 'catalog.show', ['product' => $product->slug], $product, 0.6);
        }

        foreach ($this->services->activeOrdered() as $service) {
            $this->addEntity($sitemap, 'services.show', ['service' => $service->slug], $service, 0.7);
        }

        foreach ($this->posts->activeOrdered() as $post) {
            $this->addEntity($sitemap, 'news.show', ['post' => $post->slug], $post, 0.6, Url::CHANGE_FREQUENCY_MONTHLY);
        }

        foreach ($this->albums->activeOrdered() as $album) {
            $this->addEntity($sitemap, 'media.show', ['album' => $album->slug], $album, 0.5);
        }

        foreach ($this->pages->activeOrdered() as $page) {
            if ($page->slug === 'about') {
                continue;
            }

            $this->addEntity($sitemap, 'pages.show', ['page' => $page->slug], $page, 0.4, Url::CHANGE_FREQUENCY_YEARLY);
        }

        return $sitemap;
    }

    /** @param  array<string, mixed>  $params */
    private function addEntity(
        Sitemap $sitemap,
        string $route,
        array $params,
        Model $model,
        float $priority,
        string $frequency = Url::CHANGE_FREQUENCY_MONTHLY,
    ): void {
        $this->addStatic($sitemap, $route, $priority, $frequency, $params, $model->updated_at);
    }

    /** @param  array<string, mixed>  $params */
    private function addStatic(
        Sitemap $sitemap,
        string $route,
        float $priority,
        string $frequency = Url::CHANGE_FREQUENCY_MONTHLY,
        array $params = [],
        mixed $lastModified = null,
    ): void {
        $locales = config('ghekatex.locales.available');
        $default = config('ghekatex.locales.default');

        $url = Url::create(route($route, array_merge($params, ['locale' => $default])))
            ->setPriority($priority)
            ->setChangeFrequency($frequency);

        if ($lastModified !== null) {
            $url->setLastModificationDate($lastModified);
        }

        foreach ($locales as $locale) {
            $url->addAlternate(
                route($route, array_merge($params, ['locale' => $locale])),
                config("ghekatex.locales.html.{$locale}", $locale),
            );
        }

        $sitemap->add($url);
    }
}

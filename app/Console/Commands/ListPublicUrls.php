<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\MediaAlbumRepositoryInterface;
use App\Repositories\Contracts\PageRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\ServiceRepositoryInterface;
use Illuminate\Console\Command;

/**
 * Список всех публичных адресов витрины — по одному на строку.
 *
 * Нужен статическому экспорту: обходчик не может найти внутренние ссылки в
 * ответе сервера, потому что разметку рисует Vue уже в браузере. Источник
 * тот же, что у карты сайта, только без привязки к домену и со всеми
 * языковыми версиями каждого адреса.
 */
class ListPublicUrls extends Command
{
    protected $signature = 'site:urls';

    protected $description = 'Вывести пути всех публичных страниц для статического экспорта';

    public function __construct(
        private readonly PageRepositoryInterface $pages,
        private readonly ProductCategoryRepositoryInterface $categories,
        private readonly ProductRepositoryInterface $products,
        private readonly ServiceRepositoryInterface $services,
        private readonly PostRepositoryInterface $posts,
        private readonly MediaAlbumRepositoryInterface $albums,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        foreach ($this->paths() as $path) {
            $this->line($path);
        }

        return self::SUCCESS;
    }

    /** @return array<int, string> */
    private function paths(): array
    {
        $locales = (array) config('ghekatex.locales.available');

        $suffixes = ['', '/about', '/catalog', '/services', '/news', '/media', '/contacts'];

        foreach ($this->categories->activeOrdered() as $category) {
            $suffixes[] = '/catalog/category/'.$category->slug;
        }

        foreach ($this->products->activeOrdered() as $product) {
            $suffixes[] = '/catalog/'.$product->slug;
        }

        foreach ($this->services->activeOrdered() as $service) {
            $suffixes[] = '/services/'.$service->slug;
        }

        foreach ($this->posts->activeOrdered() as $post) {
            $suffixes[] = '/news/'.$post->slug;
        }

        foreach ($this->albums->activeOrdered() as $album) {
            $suffixes[] = '/media/'.$album->slug;
        }

        foreach ($this->pages->activeOrdered() as $page) {
            // Страница «о компании» живёт на собственном маршруте
            if ($page->slug !== 'about') {
                $suffixes[] = '/'.$page->slug;
            }
        }

        $paths = [];

        foreach ($locales as $locale) {
            foreach ($suffixes as $suffix) {
                $paths[] = '/'.$locale.$suffix;
            }
        }

        return $paths;
    }
}

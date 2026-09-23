<?php

namespace App\Support\Seo;

/**
 * Набор мета-данных страницы, который уезжает во Vue и рендерится в <Head>.
 *
 * Собирается сервисом, а не контроллером: правила подстановки значений
 * по умолчанию одинаковы для всех разделов.
 */
final class SeoPayload
{
    /**
     * @param  array<string, string>  $alternates  локаль => абсолютный URL
     * @param  array<int, array<string, mixed>>  $breadcrumbs
     * @param  array<int, array<string, mixed>>  $schema  готовые блоки JSON-LD
     */
    public function __construct(
        public readonly string $title,
        public readonly ?string $description = null,
        public readonly ?string $keywords = null,
        public readonly ?string $ogTitle = null,
        public readonly ?string $ogDescription = null,
        public readonly ?string $ogImage = null,
        public readonly ?string $canonical = null,
        public readonly string $robots = 'index,follow',
        public readonly array $alternates = [],
        public readonly array $breadcrumbs = [],
        public readonly array $schema = [],
    ) {}

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'keywords' => $this->keywords,
            'og_title' => $this->ogTitle ?? $this->title,
            'og_description' => $this->ogDescription ?? $this->description,
            'og_image' => $this->ogImage,
            'canonical' => $this->canonical,
            'robots' => $this->robots,
            'alternates' => $this->alternates,
            'breadcrumbs' => $this->breadcrumbs,
            'schema' => $this->schema,
        ];
    }
}

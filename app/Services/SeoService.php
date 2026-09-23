<?php

namespace App\Services;

use App\Models\SeoMeta;
use App\Support\Seo\SeoPayload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\SchemaOrg\Schema;

/**
 * Собирает мета-данные страницы: своё из SEO-карточки, недостающее —
 * из контента сущности, затем из настроек сайта.
 */
class SeoService
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    /**
     * Мета для страницы сущности.
     *
     * @param  array<int, array{label: string, url: string|null}>  $breadcrumbs
     * @param  array<int, array<string, mixed>>  $schema
     */
    public function forModel(
        Model $model,
        string $fallbackTitle,
        ?string $fallbackDescription = null,
        ?string $fallbackImage = null,
        array $breadcrumbs = [],
        array $schema = [],
        ?string $robots = null,
    ): SeoPayload {
        $meta = $model->relationLoaded('seo') ? $model->seo : $model->seo()->first();
        $locale = app()->getLocale();

        $title = $this->translated($meta, 'title', $locale) ?: $fallbackTitle;
        $description = $this->translated($meta, 'description', $locale) ?: $fallbackDescription;

        return new SeoPayload(
            title: $this->withSuffix($title),
            description: $this->trim($description),
            keywords: $this->translated($meta, 'keywords', $locale),
            ogTitle: $this->translated($meta, 'og_title', $locale) ?: $title,
            ogDescription: $this->translated($meta, 'og_description', $locale) ?: $this->trim($description),
            ogImage: $this->absolute($meta?->og_image ?: $fallbackImage) ?: $this->defaultImage(),
            canonical: $meta?->canonical_url ?: url()->current(),
            robots: $meta?->robots ?? $robots ?? 'index,follow',
            alternates: $this->alternates(),
            breadcrumbs: $breadcrumbs,
            schema: array_merge([$this->organization()], $schema, $this->breadcrumbSchema($breadcrumbs)),
        );
    }

    /**
     * Мета для страницы без собственной сущности — списки и служебные разделы.
     *
     * @param  array<int, array{label: string, url: string|null}>  $breadcrumbs
     * @param  array<int, array<string, mixed>>  $schema
     */
    public function forPage(
        string $title,
        ?string $description = null,
        ?string $image = null,
        array $breadcrumbs = [],
        array $schema = [],
        string $robots = 'index,follow',
    ): SeoPayload {
        return new SeoPayload(
            title: $this->withSuffix($title),
            description: $this->trim($description ?? $this->settings->get('seo_default_description')),
            keywords: $this->settings->get('seo_default_keywords'),
            ogTitle: $title,
            ogDescription: $this->trim($description),
            ogImage: $image !== null ? $this->absolute($image) : $this->defaultImage(),
            canonical: url()->current(),
            robots: $robots,
            alternates: $this->alternates(),
            breadcrumbs: $breadcrumbs,
            schema: array_merge([$this->organization()], $schema, $this->breadcrumbSchema($breadcrumbs)),
        );
    }

    /**
     * Ссылки на другие языковые версии текущего URL.
     *
     * @return array<string, string>
     */
    public function alternates(): array
    {
        $available = config('ghekatex.locales.available');
        $current = app()->getLocale();
        $path = trim(request()->path(), '/');

        // Текущая локаль — первый сегмент пути; подменяем его на каждую доступную
        $segments = $path === '' ? [] : explode('/', $path);

        if ($segments !== [] && in_array($segments[0], $available, true)) {
            array_shift($segments);
        }

        $rest = implode('/', $segments);
        $result = [];

        foreach ($available as $locale) {
            $result[config("ghekatex.locales.html.{$locale}", $locale)] = url($rest === '' ? $locale : "{$locale}/{$rest}");
        }

        $result['x-default'] = url($rest === '' ? $current : "{$current}/{$rest}");

        return $result;
    }

    /** @return array<string, mixed> Микроразметка организации для всех страниц. */
    public function organization(): array
    {
        $brand = config('ghekatex.seo.organization.brand');

        return Schema::organization()
            ->name($this->settings->get('company_name', $brand))
            ->legalName(config('ghekatex.seo.organization.legal_name'))
            ->url(url('/'))
            ->logo(url('/brand/logo_horizontal_filled.svg'))
            ->email($this->settings->get('contact_email'))
            ->telephone($this->settings->get('contact_phone'))
            ->foundingDate(config('ghekatex.seo.organization.founded'))
            ->sameAs(array_values(array_filter([
                $this->settings->get('social_facebook'),
                $this->settings->get('social_instagram'),
                $this->settings->get('social_linkedin'),
            ])))
            ->toArray();
    }

    /**
     * @param  array<int, array{label: string, url: string|null}>  $items
     * @return array<int, array<string, mixed>>
     */
    private function breadcrumbSchema(array $items): array
    {
        if ($items === []) {
            return [];
        }

        $elements = [];
        $position = 1;

        foreach ($items as $item) {
            $elements[] = Schema::listItem()
                ->position($position++)
                ->name($item['label'])
                ->item($item['url'] ?? url()->current());
        }

        return [Schema::breadcrumbList()->itemListElement($elements)->toArray()];
    }

    private function translated(?SeoMeta $meta, string $field, string $locale): ?string
    {
        if ($meta === null) {
            return null;
        }

        $value = trim((string) $meta->getTranslation($field, $locale, true));

        return $value === '' ? null : $value;
    }

    private function withSuffix(string $title): string
    {
        $suffix = $this->settings->get('seo_title_suffix', config('ghekatex.seo.organization.brand'));

        if ($suffix === null || $suffix === '' || str_contains($title, (string) $suffix)) {
            return $title;
        }

        return "{$title} — {$suffix}";
    }

    private function trim(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Описание длиннее 160 символов поисковики всё равно обрежут
        return Str::limit(trim(strip_tags($value)), 160, '');
    }

    private function defaultImage(): string
    {
        $fromSettings = $this->settings->get('seo_default_og_image');

        return $this->absolute($fromSettings ?: config('ghekatex.seo.default_og_image'));
    }

    /**
     * Ссылки на медиа хранятся от корня сайта, а Open Graph требует
     * абсолютный адрес — иначе соцсеть не найдёт картинку.
     */
    private function absolute(?string $url): string
    {
        if ($url === null || $url === '') {
            return url(config('ghekatex.seo.default_og_image'));
        }

        return str_starts_with($url, 'http://') || str_starts_with($url, 'https://')
            ? $url
            : url($url);
    }
}

<?php

namespace App\Support\Presenters;

use App\Models\Advantage;
use App\Models\Certificate;
use App\Models\CompanyMilestone;
use App\Models\Facility;
use App\Models\Faq;
use App\Models\HeroSlide;
use App\Models\MediaAlbum;
use App\Models\MediaItem;
use App\Models\Office;
use App\Models\Partner;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Service;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\MediaLibrary\HasMedia;

/**
 * Превращает модели в плоские массивы для витрины: одна локаль, готовые
 * ссылки на картинки, никаких служебных полей.
 *
 * Держим форму ответа в одном месте — иначе она расползается по контроллерам
 * и Vue-страницы начинают ожидать разное от одной и той же сущности.
 */
final class SitePresenter
{
    /** @return array<string, mixed> */
    public static function heroSlide(HeroSlide $slide): array
    {
        return [
            'id' => $slide->id,
            'eyebrow' => self::text($slide, 'eyebrow'),
            'title' => self::text($slide, 'title'),
            'description' => self::text($slide, 'description'),
            'cta' => self::link(self::text($slide, 'cta_label'), $slide->cta_url),
            'secondary_cta' => self::link(self::text($slide, 'secondary_cta_label'), $slide->secondary_cta_url),
            'overlay' => $slide->overlay_opacity / 100,
            'image' => self::image($slide, 'image', 'hero'),
            'image_mobile' => self::image($slide, 'image', 'mobile'),
            'video' => self::url($slide, 'video'),
        ];
    }

    /** @return array<string, mixed> */
    public static function advantage(Advantage $item): array
    {
        return [
            'id' => $item->id,
            'icon' => $item->icon,
            'title' => self::text($item, 'title'),
            'description' => self::text($item, 'description'),
            'value' => $item->value,
            'value_suffix' => self::text($item, 'value_suffix'),
            'is_counter' => $item->is_counter,
        ];
    }

    /** @return array<string, mixed> */
    public static function milestone(CompanyMilestone $item): array
    {
        return [
            'id' => $item->id,
            'year' => $item->year,
            'title' => self::text($item, 'title'),
            'description' => self::text($item, 'description'),
        ];
    }

    /** @return array<string, mixed> */
    public static function facility(Facility $item): array
    {
        return [
            'id' => $item->id,
            'slug' => $item->slug,
            'icon' => $item->icon,
            'name' => self::text($item, 'name'),
            'summary' => self::text($item, 'summary'),
            'description' => self::text($item, 'description'),
            'specs' => self::rows($item->specs ?? [], ['label', 'value']),
            'capacity' => $item->capacity_per_month,
            'employees' => $item->employees_count,
            'cover' => self::image($item, 'cover', 'card'),
            'gallery' => self::gallery($item),
        ];
    }

    /** @return array<string, mixed> */
    public static function certificate(Certificate $item): array
    {
        return [
            'id' => $item->id,
            'name' => self::text($item, 'name'),
            'issuer' => self::text($item, 'issuer'),
            'description' => self::text($item, 'description'),
            'number' => $item->number,
            'issued_at' => $item->issued_at?->toDateString(),
            'valid_until' => $item->valid_until?->toDateString(),
            'image' => self::image($item, 'image', 'thumb'),
            'document' => self::url($item, 'document'),
        ];
    }

    /** @return array<string, mixed> */
    public static function partner(Partner $item): array
    {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'description' => self::text($item, 'description'),
            'website' => $item->website_url,
            'country' => $item->country_code,
            'logo' => self::image($item, 'logo', 'logo'),
        ];
    }

    /** @return array<string, mixed> */
    public static function productCategory(ProductCategory $item, bool $withCount = false): array
    {
        return array_filter([
            'id' => $item->id,
            'slug' => $item->slug,
            'name' => self::text($item, 'name'),
            'description' => self::text($item, 'description'),
            'cover' => self::image($item, 'cover', 'card'),
            'products_count' => $withCount ? $item->products_count : null,
        ], static fn ($value) => $value !== null);
    }

    /** @return array<string, mixed> */
    public static function product(Product $item, bool $full = false): array
    {
        $data = [
            'id' => $item->id,
            'slug' => $item->slug,
            'article' => $item->article,
            'name' => self::text($item, 'name'),
            'summary' => self::text($item, 'short_description'),
            'cover' => self::image($item, 'cover', 'card'),
            'thumb' => self::image($item, 'cover', 'thumb'),
            'category' => $item->relationLoaded('category') && $item->category !== null
                ? ['slug' => $item->category->slug, 'name' => self::text($item->category, 'name')]
                : null,
        ];

        if (! $full) {
            return $data;
        }

        return array_merge($data, [
            'description' => self::text($item, 'description'),
            'composition' => self::text($item, 'composition'),
            'attributes' => self::rows($item->attributes ?? [], ['label', 'value']),
            'min_order' => $item->min_order_quantity,
            'lead_time' => $item->lead_time_days,
            'gallery' => self::gallery($item, 'gallery', 'card'),
            'fabrics' => $item->fabrics->map(fn ($fabric) => [
                'slug' => $fabric->slug,
                'name' => self::text($fabric, 'name'),
                'composition' => self::text($fabric, 'composition'),
                'weight' => $fabric->weight_gsm,
                'color' => $fabric->color_hex,
                'swatch' => self::image($fabric, 'swatch', 'swatch'),
            ])->all(),
            'treatments' => $item->treatments->map(fn ($treatment) => [
                'slug' => $treatment->slug,
                'name' => self::text($treatment, 'name'),
                'icon' => $treatment->icon,
            ])->all(),
        ]);
    }

    /** @return array<string, mixed> */
    public static function service(Service $item, bool $full = false): array
    {
        $data = [
            'id' => $item->id,
            'slug' => $item->slug,
            'icon' => $item->icon,
            'name' => self::text($item, 'name'),
            'summary' => self::text($item, 'short_description'),
            'cover' => self::image($item, 'cover', 'card'),
        ];

        if (! $full) {
            return $data;
        }

        return array_merge($data, [
            'description' => self::text($item, 'description'),
            'lead_time' => self::text($item, 'lead_time'),
            'highlights' => self::rows($item->highlights ?? [], ['label', 'value'], ['icon']),
            'process_steps' => self::rows($item->process_steps ?? [], ['title', 'text']),
            'gallery' => self::gallery($item),
        ]);
    }

    /** @return array<string, mixed> */
    public static function post(Post $item, bool $full = false): array
    {
        $data = [
            'id' => $item->id,
            'slug' => $item->slug,
            'type' => $item->type->value,
            'title' => self::text($item, 'title'),
            'excerpt' => self::text($item, 'excerpt'),
            'published_at' => $item->published_at?->toIso8601String(),
            'reading' => $item->reading_minutes,
            'cover' => self::image($item, 'cover', 'card'),
            'thumb' => self::image($item, 'cover', 'thumb'),
            'category' => $item->relationLoaded('category') && $item->category !== null
                ? ['slug' => $item->category->slug, 'name' => self::text($item->category, 'name')]
                : null,
        ];

        if (! $full) {
            return $data;
        }

        return array_merge($data, [
            'body' => self::text($item, 'body'),
            'wide' => self::image($item, 'cover', 'wide'),
            'gallery' => self::gallery($item),
            'author' => $item->author?->name,
        ]);
    }

    /** @return array<string, mixed> */
    public static function album(MediaAlbum $item, bool $withItems = false): array
    {
        return array_filter([
            'id' => $item->id,
            'slug' => $item->slug,
            'title' => self::text($item, 'title'),
            'description' => self::text($item, 'description'),
            'cover' => $item->relationLoaded('items')
                ? self::image($item->items->first(), 'file', 'thumb')
                : null,
            'count' => $item->items_count ?? null,
            // Лента превью под обложкой: несколько первых кадров альбома
            'previews' => $item->relationLoaded('items') && ! $withItems
                ? $item->items->skip(1)->map(fn (MediaItem $media) => self::image($media, 'file', 'thumb'))->filter()->values()->all()
                : null,
            'items' => $withItems
                ? $item->items->map(fn (MediaItem $media) => self::mediaItem($media))->all()
                : null,
        ], static fn ($value) => $value !== null);
    }

    /** @return array<string, mixed> */
    public static function mediaItem(MediaItem $item): array
    {
        return [
            'id' => $item->id,
            'type' => $item->type->value,
            'title' => self::text($item, 'title'),
            'caption' => self::text($item, 'caption'),
            'provider' => $item->video_provider?->value,
            'video' => $item->video_url ?: self::url($item, 'file'),
            'thumb' => self::image($item, 'file', 'thumb') ?? self::image($item, 'poster', 'thumb'),
            'full' => self::image($item, 'file', 'full') ?? self::image($item, 'poster', 'full'),
        ];
    }

    /** @return array{id: int, question: string|null, answer: string|null} */
    public static function faq(Faq $item): array
    {
        return [
            'id' => $item->id,
            'question' => self::text($item, 'question'),
            'answer' => self::text($item, 'answer'),
        ];
    }

    /** @return array<string, mixed> */
    public static function office(Office $item): array
    {
        return [
            'id' => $item->id,
            'type' => $item->type->value,
            'name' => self::text($item, 'name'),
            'address' => self::text($item, 'address'),
            'city' => self::text($item, 'city'),
            'country' => $item->country_code,
            'postal' => $item->postal_code,
            'phones' => $item->phones ?? [],
            'emails' => $item->emails ?? [],
            'hours' => self::text($item, 'working_hours'),
            'latitude' => $item->latitude,
            'longitude' => $item->longitude,
            'primary' => $item->is_primary,
            'photo' => self::image($item, 'photo', 'card'),
        ];
    }

    /**
     * @template T of Model
     *
     * @param  Collection<int, T>|array<int, T>  $items
     * @return array<int, array<string, mixed>>
     */
    public static function collect(iterable $items, callable $presenter): array
    {
        $result = [];

        foreach ($items as $item) {
            $result[] = $presenter($item);
        }

        return $result;
    }

    /** Перевод поля в текущей локали с откатом на основную. */
    private static function text(?Model $model, string $field): ?string
    {
        if ($model === null || ! method_exists($model, 'getTranslation')) {
            return null;
        }

        $value = trim((string) $model->getTranslation($field, app()->getLocale(), true));

        return $value === '' ? null : $value;
    }

    /**
     * Список-структуру приводим к одной локали.
     *
     * @param  array<int, array<string, mixed>>  $rows
     * @param  array<int, string>  $translatable
     * @param  array<int, string>  $plain
     * @return array<int, array<string, mixed>>
     */
    private static function rows(array $rows, array $translatable, array $plain = []): array
    {
        $locale = app()->getLocale();
        $fallback = config('ghekatex.locales.default');
        $result = [];

        foreach ($rows as $row) {
            $item = [];

            foreach ($translatable as $key) {
                $pair = is_array($row[$key] ?? null) ? $row[$key] : [];
                $item[$key] = trim((string) ($pair[$locale] ?? $pair[$fallback] ?? ''));
            }

            foreach ($plain as $key) {
                $item[$key] = $row[$key] ?? null;
            }

            if (implode('', array_map(static fn ($v) => (string) $v, array_values($item))) !== '') {
                $result[] = $item;
            }
        }

        return $result;
    }

    private static function image(?Model $model, string $collection, string $conversion): ?string
    {
        if (! $model instanceof HasMedia) {
            return null;
        }

        $media = $model->getFirstMedia($collection);

        if ($media === null) {
            return null;
        }

        return $media->hasGeneratedConversion($conversion)
            ? $media->getUrl($conversion)
            : $media->getUrl();
    }

    private static function url(?Model $model, string $collection): ?string
    {
        if (! $model instanceof HasMedia) {
            return null;
        }

        return $model->getFirstMediaUrl($collection) ?: null;
    }

    /** @return array<int, array{url: string, thumb: string, alt: string|null}> */
    private static function gallery(Model $model, string $collection = 'gallery', string $conversion = 'card'): array
    {
        if (! $model instanceof HasMedia) {
            return [];
        }

        return $model->getMedia($collection)
            ->map(fn ($media) => [
                'url' => $media->hasGeneratedConversion($conversion) ? $media->getUrl($conversion) : $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                'alt' => $media->getCustomProperty('alt'),
            ])
            ->all();
    }

    /** @return array{label: string, url: string}|null */
    private static function link(?string $label, ?string $url): ?array
    {
        if ($label === null || $label === '' || $url === null || $url === '') {
            return null;
        }

        return ['label' => $label, 'url' => $url];
    }
}

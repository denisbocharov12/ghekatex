<?php

namespace App\Managers;

use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\MediaLibrary\HasMedia;

/**
 * Общие инварианты контентных разделов: транзакция на запись, уникальный слаг,
 * позиция в списке, одиночные и множественные медиа.
 *
 * Наследник описывает специфику раздела, а не повторяет эти шаги.
 */
abstract class BaseContentManager
{
    public function __construct(
        protected readonly CrudRepositoryInterface $repository,
    ) {}

    /** @return class-string<Model> */
    abstract protected function modelClass(): string;

    /** Поле, из которого строится слаг, если он не задан вручную. */
    protected function slugSource(): ?string
    {
        return 'name';
    }

    /**
     * Создание записи с медиа.
     *
     * @param  array<string, mixed>  $attributes
     * @param  array<string, UploadedFile|null>  $singles  коллекция => файл
     * @param  array<int, UploadedFile>  $gallery
     */
    public function store(array $attributes, array $singles = [], array $gallery = []): Model
    {
        return DB::transaction(function () use ($attributes, $singles, $gallery) {
            $attributes = $this->withSlug($attributes);
            $attributes = $this->withSortOrder($attributes);

            $model = $this->repository->create($attributes);

            $this->syncSingles($model, $singles);
            $this->appendGallery($model, $gallery);

            return $model;
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, UploadedFile|null>  $singles
     * @param  array<int, UploadedFile>  $gallery
     */
    public function modify(Model $model, array $attributes, array $singles = [], array $gallery = []): Model
    {
        return DB::transaction(function () use ($model, $attributes, $singles, $gallery) {
            $attributes = $this->withSlug($attributes, $model);

            $model = $this->repository->update($model, $attributes);

            $this->syncSingles($model, $singles);
            $this->appendGallery($model, $gallery);

            return $model;
        });
    }

    public function remove(Model $model): void
    {
        DB::transaction(fn () => $this->repository->delete($model));
    }

    /** @param  array<int, array{id: int, sort_order: int}>  $rows */
    public function reorder(array $rows): void
    {
        $this->repository->reorder($rows);
    }

    public function toggleActive(Model $model): Model
    {
        return $this->repository->update($model, ['is_active' => ! $model->is_active]);
    }

    /**
     * Уникальный слаг. Пустой достраивается из основной локали заголовка,
     * коллизия разрешается числовым суффиксом.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function withSlug(array $attributes, ?Model $existing = null): array
    {
        $source = $this->slugSource();

        if ($source === null) {
            return $attributes;
        }

        $slug = trim((string) ($attributes['slug'] ?? ''));

        if ($slug === '') {
            $primary = config('ghekatex.locales.default');
            $value = $attributes[$source] ?? [];
            $raw = is_array($value)
                ? ($value[$primary] ?? reset($value) ?: '')
                : (string) $value;

            $slug = Str::slug((string) $raw);
        }

        $slug = $slug !== '' ? Str::slug($slug) : Str::random(8);
        $attributes['slug'] = $this->uniqueSlug($slug, $existing?->getKey());

        return $attributes;
    }

    protected function uniqueSlug(string $slug, ?int $ignoreId = null): string
    {
        $model = $this->modelClass();
        $candidate = $slug;
        $suffix = 2;

        while ($model::query()
            ->where('slug', $candidate)
            ->when($ignoreId !== null, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()
        ) {
            $candidate = $slug.'-'.$suffix++;
        }

        return $candidate;
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    protected function withSortOrder(array $attributes): array
    {
        $model = $this->modelClass();

        if (! array_key_exists('sort_order', $attributes) || $attributes['sort_order'] === null) {
            $attributes['sort_order'] = method_exists($model, 'nextSortOrder')
                ? $model::nextSortOrder()
                : 0;
        }

        return $attributes;
    }

    /**
     * Одиночные коллекции: новый файл вытесняет прежний, `null` ничего не трогает.
     *
     * @param  array<string, UploadedFile|null>  $singles
     */
    protected function syncSingles(Model $model, array $singles): void
    {
        if (! $model instanceof HasMedia) {
            return;
        }

        foreach ($singles as $collection => $file) {
            if ($file instanceof UploadedFile) {
                $model->addMedia($file)->toMediaCollection($collection);
            }
        }
    }

    /** @param  array<int, UploadedFile>  $files */
    protected function appendGallery(Model $model, array $files, string $collection = 'gallery'): void
    {
        if (! $model instanceof HasMedia) {
            return;
        }

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $model->addMedia($file)->toMediaCollection($collection);
            }
        }
    }

    /**
     * Галерея для формы админки: идентификатор, ссылка и позиция.
     *
     * @return array<int, array{id: int, url: string, thumb: string, name: string, order: int}>
     */
    public function galleryPayload(Model $model, string $collection = 'gallery'): array
    {
        if (! $model instanceof HasMedia) {
            return [];
        }

        return $model->getMedia($collection)
            ->map(fn ($media) => [
                'id' => $media->id,
                'url' => $media->getUrl(),
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl(),
                'name' => $media->file_name,
                'order' => (int) $media->order_column,
            ])
            ->values()
            ->all();
    }

    public function deleteMedia(Model $model, int $mediaId): void
    {
        if (! $model instanceof HasMedia) {
            return;
        }

        $model->media()->whereKey($mediaId)->first()?->delete();
    }

    /** @param  array<int, int>  $orderedIds */
    public function reorderMedia(Model $model, array $orderedIds): void
    {
        if (! $model instanceof HasMedia) {
            return;
        }

        $mediaClass = config('media-library.media_model');
        $mediaClass::setNewOrder($orderedIds);
    }
}

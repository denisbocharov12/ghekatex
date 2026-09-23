<?php

namespace App\Data;

use Illuminate\Http\UploadedFile;
use Spatie\LaravelData\Data;

/**
 * Нормализованные данные формы админки: атрибуты модели, файлы и связи.
 */
class ResourceData extends Data
{
    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<string, UploadedFile|null>  $singles  коллекция => файл
     * @param  array<int, UploadedFile>  $gallery
     * @param  array<string, array<int, int>>  $relations  связь => идентификаторы
     * @param  array<string, mixed>  $seo
     */
    public function __construct(
        public readonly array $attributes,
        public readonly array $singles = [],
        public readonly array $gallery = [],
        public readonly array $relations = [],
        public readonly array $seo = [],
    ) {}

    /** @return array<string, mixed> */
    public function toModelAttributes(): array
    {
        return $this->attributes;
    }
}

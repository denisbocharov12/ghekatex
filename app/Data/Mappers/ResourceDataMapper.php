<?php

namespace App\Data\Mappers;

use App\Data\ResourceData;
use App\Data\Schemas\FieldSchema;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

/**
 * Превращает форму админки в данные для менеджера по описанию полей раздела.
 *
 * Здесь же гасятся типичные капризы HTML-форм: чекбокс приходит строкой
 * «0»/«1», число — строкой, незаполненный перевод — отсутствующим ключом.
 */
final class ResourceDataMapper
{
    public static function fromRequest(FormRequest $request, FieldSchema $schema): ResourceData
    {
        $attributes = [];

        foreach ($schema->translatable as $field) {
            $attributes[$field] = TranslationNormalizer::pairs($request->input($field));
        }

        foreach ($schema->plain as $field) {
            $attributes[$field] = self::cast($request->input($field), $schema->casts[$field] ?? 'string');
        }

        foreach ($schema->rows as $field => $definition) {
            $attributes[$field] = TranslationNormalizer::rows(
                $request->input($field),
                $definition['translatable'],
                $definition['plain'] ?? [],
                $definition['required'] ?? ($definition['translatable'][0] ?? 'title'),
            );
        }

        $singles = [];

        foreach ($schema->singleMedia as $collection) {
            $file = $request->file($collection);
            $singles[$collection] = $file instanceof UploadedFile ? $file : null;
        }

        $gallery = array_values(array_filter(
            (array) $request->file('gallery', []),
            static fn ($file) => $file instanceof UploadedFile,
        ));

        $relations = [];

        foreach ($schema->relations as $relation) {
            $relations[$relation] = array_values(array_filter(array_map(
                static fn ($id) => (int) $id,
                (array) $request->input($relation, []),
            )));
        }

        return new ResourceData(
            attributes: $attributes,
            singles: $singles,
            gallery: $gallery,
            relations: $relations,
            seo: self::seo($request),
        );
    }

    /**
     * Блок SEO присутствует не в каждой форме — пустой массив ничего не меняет.
     *
     * @return array<string, mixed>
     */
    private static function seo(FormRequest $request): array
    {
        $input = $request->input('seo');

        if (! is_array($input) || $input === []) {
            return [];
        }

        return [
            'title' => TranslationNormalizer::pairs($input['title'] ?? []),
            'description' => TranslationNormalizer::pairs($input['description'] ?? []),
            'keywords' => TranslationNormalizer::pairs($input['keywords'] ?? []),
            'og_title' => TranslationNormalizer::pairs($input['og_title'] ?? []),
            'og_description' => TranslationNormalizer::pairs($input['og_description'] ?? []),
            'og_image' => TranslationNormalizer::nullableString($input['og_image'] ?? null),
            'canonical_url' => TranslationNormalizer::nullableString($input['canonical_url'] ?? null),
            'robots' => TranslationNormalizer::nullableString($input['robots'] ?? null) ?? 'index,follow',
            'sitemap_priority' => (float) ($input['sitemap_priority'] ?? 0.5),
            'sitemap_frequency' => TranslationNormalizer::nullableString($input['sitemap_frequency'] ?? null) ?? 'monthly',
        ];
    }

    private static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'bool' => filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false,
            'int' => $value === null || $value === '' ? null : (int) $value,
            'float' => $value === null || $value === '' ? null : (float) $value,
            'array' => is_array($value) ? $value : [],
            'list' => TranslationNormalizer::stringList($value),
            'date' => TranslationNormalizer::nullableString($value),
            default => TranslationNormalizer::nullableString($value),
        };
    }
}

<?php

namespace App\Data\Mappers;

/**
 * Приводит переводимые значения к полному набору локалей проекта.
 *
 * Формы админки присылают только заполненные языки, а `spatie/laravel-translatable`
 * должен получить предсказуемую структуру — иначе пустой перевод «залипает»
 * значением из другой локали.
 */
final class TranslationNormalizer
{
    /**
     * @param  mixed  $value  массив вида `['ro' => '...', 'en' => '...']`
     * @return array<string, string>
     */
    public static function pairs(mixed $value): array
    {
        $value = is_array($value) ? $value : [];
        $result = [];

        foreach (config('ghekatex.locales.available') as $locale) {
            $result[$locale] = trim((string) ($value[$locale] ?? ''));
        }

        return $result;
    }

    /**
     * То же, но для списков-структур: каждый элемент содержит переводимые ключи.
     *
     * @param  array<int, string>  $translatableKeys
     * @param  array<int, string>  $plainKeys
     * @param  string  $requiredKey  элемент без значения этого ключа в основной локали отбрасывается
     * @return array<int, array<string, mixed>>
     */
    public static function rows(mixed $rows, array $translatableKeys, array $plainKeys = [], string $requiredKey = 'title'): array
    {
        $rows = is_array($rows) ? $rows : [];
        $primary = config('ghekatex.locales.default');
        $result = [];

        foreach ($rows as $row) {
            if (! is_array($row)) {
                continue;
            }

            $item = [];

            foreach ($translatableKeys as $key) {
                $item[$key] = self::pairs($row[$key] ?? []);
            }

            foreach ($plainKeys as $key) {
                $item[$key] = is_scalar($row[$key] ?? null) ? $row[$key] : null;
            }

            if (isset($item[$requiredKey]) && ($item[$requiredKey][$primary] ?? '') === '') {
                continue;
            }

            $result[] = $item;
        }

        return $result;
    }

    /** Строка без пробелов по краям; пустая превращается в null. */
    public static function nullableString(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return $value === '' ? null : $value;
    }

    /**
     * Плоский список строк без пустых значений — телефоны, e-mail, ссылки.
     *
     * @return array<int, string>
     */
    public static function stringList(mixed $value): array
    {
        $value = is_array($value) ? $value : [];

        return array_values(array_filter(array_map(
            static fn ($item) => trim((string) $item),
            $value,
        ), static fn (string $item) => $item !== ''));
    }
}

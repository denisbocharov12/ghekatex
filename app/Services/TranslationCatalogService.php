<?php

namespace App\Services;

use App\Models\TranslationOverride;
use App\Repositories\Contracts\TranslationOverrideRepositoryInterface;
use Illuminate\Support\Facades\Cache;

/**
 * Правки текстов интерфейса, сделанные в админке.
 *
 * Словари витрины и панели лежат в JSON-файлах и обновляются с релизами;
 * правки редактора хранятся в БД и накладываются сверху. Так выкладка
 * новой версии не затирает чужую работу, а редактор не ждёт разработчика.
 */
class TranslationCatalogService
{
    public function __construct(
        private readonly TranslationOverrideRepositoryInterface $overrides,
    ) {}

    /**
     * Правки одной группы: `site`, `admin` или группа PHP-файлов.
     *
     * @return array<string, string>
     */
    public function overrides(string $locale, string $group): array
    {
        return Cache::rememberForever(
            "ghekatex.translations.{$locale}.{$group}",
            fn () => $this->overrides->all()
                ->where('locale', $locale)
                ->where('group', $group)
                ->mapWithKeys(fn (TranslationOverride $row) => [$row->key => (string) $row->value])
                ->all(),
        );
    }

    /**
     * Каталог для экрана «Переводы»: ключ, исходное значение и правка по локалям.
     *
     * @param  array<string, string>  $source  ключ => значение из JSON-словаря
     * @return array<int, array<string, mixed>>
     */
    public function catalog(string $group, array $source): array
    {
        $locales = config('ghekatex.locales.available');
        $byLocale = [];

        foreach ($locales as $locale) {
            $byLocale[$locale] = $this->overrides($locale, $group);
        }

        $keys = array_unique(array_merge(
            array_keys($source),
            ...array_map('array_keys', array_values($byLocale)),
        ));
        sort($keys);

        return array_map(static function (string $key) use ($source, $byLocale, $locales) {
            $values = [];

            foreach ($locales as $locale) {
                $values[$locale] = $byLocale[$locale][$key] ?? '';
            }

            return [
                'key' => $key,
                'source' => $source[$key] ?? '',
                'values' => $values,
                'modified' => array_filter($values) !== [],
            ];
        }, $keys);
    }

    /**
     * Сохраняет правки одной строки по всем локалям.
     * Пустое значение удаляет правку и возвращает строку к словарю.
     *
     * @param  array<string, string>  $values
     */
    public function put(string $group, string $key, array $values): void
    {
        foreach ($values as $locale => $value) {
            $value = trim((string) $value);

            if ($value === '') {
                TranslationOverride::query()
                    ->where(compact('locale', 'group', 'key'))
                    ->delete();

                continue;
            }

            TranslationOverride::query()->updateOrCreate(
                compact('locale', 'group', 'key'),
                ['value' => $value],
            );
        }

        $this->flush($group);
    }

    public function flush(?string $group = null): void
    {
        $groups = $group !== null ? [$group] : ['site', 'admin'];

        foreach (config('ghekatex.locales.available') as $locale) {
            foreach ($groups as $item) {
                Cache::forget("ghekatex.translations.{$locale}.{$item}");
            }
        }
    }
}

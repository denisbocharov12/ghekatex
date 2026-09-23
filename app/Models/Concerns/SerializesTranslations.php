<?php

namespace App\Models\Concerns;

/**
 * Отдаёт переводимые поля наружу как полный набор локалей.
 *
 * Без этого `toArray()` вернёт строку текущей локали, и форма админки получит
 * не то, что ожидает: ей нужны все языки сразу.
 */
trait SerializesTranslations
{
    /** @return array<string, mixed> */
    public function toArray(): array
    {
        $attributes = parent::toArray();

        foreach ($this->getTranslatableAttributes() as $field) {
            $attributes[$field] = $this->normalizedTranslations($field);
        }

        return $attributes;
    }

    /**
     * Переводы поля со всеми локалями проекта: недостающие — пустой строкой.
     *
     * @return array<string, string>
     */
    public function normalizedTranslations(string $field): array
    {
        $stored = $this->getTranslations($field);
        $result = [];

        foreach (config('ghekatex.locales.available') as $locale) {
            $result[$locale] = (string) ($stored[$locale] ?? '');
        }

        return $result;
    }
}

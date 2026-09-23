<?php

namespace Database\Seeders\Concerns;

/**
 * Короткая запись переводов в сидерах: `$this->t('ro', 'en', 'ru')`.
 * Без неё каждая строка контента превращается в трёхстрочный массив.
 */
trait TranslatesSeedData
{
    /** @return array<string, string> */
    protected function t(string $ro, string $en, string $ru): array
    {
        return ['ro' => $ro, 'en' => $en, 'ru' => $ru];
    }
}

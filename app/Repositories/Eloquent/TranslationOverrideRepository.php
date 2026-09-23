<?php

namespace App\Repositories\Eloquent;

use App\Models\TranslationOverride;
use App\Repositories\Contracts\TranslationOverrideRepositoryInterface;

/**
 * @extends BaseRepository<TranslationOverride>
 */
class TranslationOverrideRepository extends BaseRepository implements TranslationOverrideRepositoryInterface
{
    protected function model(): string
    {
        return TranslationOverride::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['key', 'value'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['locale', 'group'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['key', 'locale', 'updated_at'];
    }

    protected function defaultSort(): string
    {
        return 'key';
    }
}

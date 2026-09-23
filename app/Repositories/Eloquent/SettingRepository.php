<?php

namespace App\Repositories\Eloquent;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;

/**
 * @extends BaseRepository<Setting>
 */
class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    protected function model(): string
    {
        return Setting::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['key', 'label'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['group', 'type'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'key'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Office;
use App\Repositories\Contracts\OfficeRepositoryInterface;

/**
 * @extends BaseRepository<Office>
 */
class OfficeRepository extends BaseRepository implements OfficeRepositoryInterface
{
    protected function model(): string
    {
        return Office::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['postal_code'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'type', 'country_code'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'type'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

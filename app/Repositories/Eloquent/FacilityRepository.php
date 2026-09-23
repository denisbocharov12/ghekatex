<?php

namespace App\Repositories\Eloquent;

use App\Models\Facility;
use App\Repositories\Contracts\FacilityRepositoryInterface;

/**
 * @extends BaseRepository<Facility>
 */
class FacilityRepository extends BaseRepository implements FacilityRepositoryInterface
{
    protected function model(): string
    {
        return Facility::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['slug'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'id', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

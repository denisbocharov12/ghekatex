<?php

namespace App\Repositories\Eloquent;

use App\Models\Advantage;
use App\Repositories\Contracts\AdvantageRepositoryInterface;

/**
 * @extends BaseRepository<Advantage>
 */
class AdvantageRepository extends BaseRepository implements AdvantageRepositoryInterface
{
    protected function model(): string
    {
        return Advantage::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['icon'];
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

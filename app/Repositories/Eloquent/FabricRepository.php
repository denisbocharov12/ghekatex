<?php

namespace App\Repositories\Eloquent;

use App\Models\Fabric;
use App\Repositories\Contracts\FabricRepositoryInterface;

/**
 * @extends BaseRepository<Fabric>
 */
class FabricRepository extends BaseRepository implements FabricRepositoryInterface
{
    protected function model(): string
    {
        return Fabric::class;
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
        return ['sort_order', 'weight_gsm'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

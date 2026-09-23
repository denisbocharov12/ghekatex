<?php

namespace App\Repositories\Eloquent;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;

/**
 * @extends BaseRepository<Service>
 */
class ServiceRepository extends BaseRepository implements ServiceRepositoryInterface
{
    protected function model(): string
    {
        return Service::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['slug'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'is_featured'];
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

    /** @return array<int, string> */
    protected function listRelations(): array
    {
        return ['media'];
    }
}

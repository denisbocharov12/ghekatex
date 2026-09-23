<?php

namespace App\Repositories\Eloquent;

use App\Models\Treatment;
use App\Repositories\Contracts\TreatmentRepositoryInterface;

/**
 * @extends BaseRepository<Treatment>
 */
class TreatmentRepository extends BaseRepository implements TreatmentRepositoryInterface
{
    protected function model(): string
    {
        return Treatment::class;
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

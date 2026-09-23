<?php

namespace App\Repositories\Eloquent;

use App\Models\Partner;
use App\Repositories\Contracts\PartnerRepositoryInterface;

/**
 * @extends BaseRepository<Partner>
 */
class PartnerRepository extends BaseRepository implements PartnerRepositoryInterface
{
    protected function model(): string
    {
        return Partner::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['name'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'is_featured', 'country_code'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'name'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

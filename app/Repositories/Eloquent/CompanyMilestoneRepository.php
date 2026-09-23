<?php

namespace App\Repositories\Eloquent;

use App\Models\CompanyMilestone;
use App\Repositories\Contracts\CompanyMilestoneRepositoryInterface;

/**
 * @extends BaseRepository<CompanyMilestone>
 */
class CompanyMilestoneRepository extends BaseRepository implements CompanyMilestoneRepositoryInterface
{
    protected function model(): string
    {
        return CompanyMilestone::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['year'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'year'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

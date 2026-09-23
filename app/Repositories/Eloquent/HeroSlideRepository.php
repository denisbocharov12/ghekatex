<?php

namespace App\Repositories\Eloquent;

use App\Models\HeroSlide;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;

/**
 * @extends BaseRepository<HeroSlide>
 */
class HeroSlideRepository extends BaseRepository implements HeroSlideRepositoryInterface
{
    protected function model(): string
    {
        return HeroSlide::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['cta_url'];
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

<?php

namespace App\Repositories\Eloquent;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;

/**
 * @extends BaseRepository<Page>
 */
class PageRepository extends BaseRepository implements PageRepositoryInterface
{
    protected function model(): string
    {
        return Page::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['slug'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'template', 'is_system'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'slug', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

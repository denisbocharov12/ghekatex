<?php

namespace App\Repositories\Eloquent;

use App\Models\PostCategory;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;

/**
 * @extends BaseRepository<PostCategory>
 */
class PostCategoryRepository extends BaseRepository implements PostCategoryRepositoryInterface
{
    protected function model(): string
    {
        return PostCategory::class;
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

<?php

namespace App\Repositories\Eloquent;

use App\Models\Redirect;
use App\Repositories\Contracts\RedirectRepositoryInterface;

/**
 * @extends BaseRepository<Redirect>
 */
class RedirectRepository extends BaseRepository implements RedirectRepositoryInterface
{
    protected function model(): string
    {
        return Redirect::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['from_path', 'to_path'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['created_at', 'hits_count'];
    }

    protected function defaultSort(): string
    {
        return '-created_at';
    }
}

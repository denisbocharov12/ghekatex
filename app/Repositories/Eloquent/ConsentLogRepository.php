<?php

namespace App\Repositories\Eloquent;

use App\Models\ConsentLog;
use App\Repositories\Contracts\ConsentLogRepositoryInterface;

/**
 * @extends BaseRepository<ConsentLog>
 */
class ConsentLogRepository extends BaseRepository implements ConsentLogRepositoryInterface
{
    protected function model(): string
    {
        return ConsentLog::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['anonymous_id'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['policy_version', 'locale'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['created_at'];
    }

    protected function defaultSort(): string
    {
        return '-created_at';
    }
}

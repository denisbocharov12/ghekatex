<?php

namespace App\Repositories\Eloquent;

use App\Models\Subscriber;
use App\Repositories\Contracts\SubscriberRepositoryInterface;

/**
 * @extends BaseRepository<Subscriber>
 */
class SubscriberRepository extends BaseRepository implements SubscriberRepositoryInterface
{
    protected function model(): string
    {
        return Subscriber::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['email'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['locale'];
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

<?php

namespace App\Repositories\Eloquent;

use App\Models\ContactRequest;
use App\Repositories\Contracts\ContactRequestRepositoryInterface;

/**
 * @extends BaseRepository<ContactRequest>
 */
class ContactRequestRepository extends BaseRepository implements ContactRequestRepositoryInterface
{
    protected function model(): string
    {
        return ContactRequest::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['name', 'email', 'company', 'subject'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['status', 'source', 'locale'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['created_at', 'status'];
    }

    protected function defaultSort(): string
    {
        return '-created_at';
    }

    /** @return array<int, string> */
    protected function listRelations(): array
    {
        return ['handler'];
    }
}

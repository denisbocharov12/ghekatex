<?php

namespace App\Repositories\Eloquent;

use App\Models\Certificate;
use App\Repositories\Contracts\CertificateRepositoryInterface;

/**
 * @extends BaseRepository<Certificate>
 */
class CertificateRepository extends BaseRepository implements CertificateRepositoryInterface
{
    protected function model(): string
    {
        return Certificate::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['number'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'issued_at', 'valid_until'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }
}

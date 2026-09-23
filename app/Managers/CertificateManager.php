<?php

namespace App\Managers;

use App\Models\Certificate;
use App\Repositories\Contracts\CertificateRepositoryInterface;

class CertificateManager extends BaseContentManager
{
    public function __construct(CertificateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Certificate::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

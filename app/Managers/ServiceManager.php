<?php

namespace App\Managers;

use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;

class ServiceManager extends BaseContentManager
{
    public function __construct(ServiceRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Service::class;
    }

    protected function slugSource(): ?string
    {
        return 'name';
    }
}

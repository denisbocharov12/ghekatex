<?php

namespace App\Managers;

use App\Models\Office;
use App\Repositories\Contracts\OfficeRepositoryInterface;

class OfficeManager extends BaseContentManager
{
    public function __construct(OfficeRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Office::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

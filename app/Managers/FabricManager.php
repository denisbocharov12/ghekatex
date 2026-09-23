<?php

namespace App\Managers;

use App\Models\Fabric;
use App\Repositories\Contracts\FabricRepositoryInterface;

class FabricManager extends BaseContentManager
{
    public function __construct(FabricRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Fabric::class;
    }

    protected function slugSource(): ?string
    {
        return 'name';
    }
}

<?php

namespace App\Managers;

use App\Models\Facility;
use App\Repositories\Contracts\FacilityRepositoryInterface;

class FacilityManager extends BaseContentManager
{
    public function __construct(FacilityRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Facility::class;
    }

    protected function slugSource(): ?string
    {
        return 'name';
    }
}

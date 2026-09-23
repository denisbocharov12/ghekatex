<?php

namespace App\Managers;

use App\Models\Treatment;
use App\Repositories\Contracts\TreatmentRepositoryInterface;

class TreatmentManager extends BaseContentManager
{
    public function __construct(TreatmentRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Treatment::class;
    }

    protected function slugSource(): ?string
    {
        return 'name';
    }
}

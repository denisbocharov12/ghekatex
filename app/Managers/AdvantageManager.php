<?php

namespace App\Managers;

use App\Models\Advantage;
use App\Repositories\Contracts\AdvantageRepositoryInterface;

class AdvantageManager extends BaseContentManager
{
    public function __construct(AdvantageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Advantage::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

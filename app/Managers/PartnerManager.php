<?php

namespace App\Managers;

use App\Models\Partner;
use App\Repositories\Contracts\PartnerRepositoryInterface;

class PartnerManager extends BaseContentManager
{
    public function __construct(PartnerRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Partner::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

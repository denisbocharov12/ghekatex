<?php

namespace App\Managers;

use App\Models\CompanyMilestone;
use App\Repositories\Contracts\CompanyMilestoneRepositoryInterface;

class CompanyMilestoneManager extends BaseContentManager
{
    public function __construct(CompanyMilestoneRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return CompanyMilestone::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

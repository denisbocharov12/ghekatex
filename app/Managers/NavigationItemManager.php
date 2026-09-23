<?php

namespace App\Managers;

use App\Models\NavigationItem;
use App\Repositories\Contracts\NavigationItemRepositoryInterface;

class NavigationItemManager extends BaseContentManager
{
    public function __construct(NavigationItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return NavigationItem::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

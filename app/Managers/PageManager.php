<?php

namespace App\Managers;

use App\Models\Page;
use App\Repositories\Contracts\PageRepositoryInterface;

class PageManager extends BaseContentManager
{
    public function __construct(PageRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Page::class;
    }

    protected function slugSource(): ?string
    {
        return 'title';
    }
}

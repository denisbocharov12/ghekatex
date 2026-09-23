<?php

namespace App\Managers;

use App\Models\PostCategory;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;

class PostCategoryManager extends BaseContentManager
{
    public function __construct(PostCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return PostCategory::class;
    }

    protected function slugSource(): ?string
    {
        return 'name';
    }
}

<?php

namespace App\Managers;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostManager extends BaseContentManager
{
    public function __construct(PostRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Post::class;
    }

    protected function slugSource(): ?string
    {
        return 'title';
    }
}

<?php

namespace App\Managers;

use App\Models\HeroSlide;
use App\Repositories\Contracts\HeroSlideRepositoryInterface;

class HeroSlideManager extends BaseContentManager
{
    public function __construct(HeroSlideRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return HeroSlide::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

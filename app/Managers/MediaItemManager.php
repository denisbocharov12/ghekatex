<?php

namespace App\Managers;

use App\Models\MediaItem;
use App\Repositories\Contracts\MediaItemRepositoryInterface;

class MediaItemManager extends BaseContentManager
{
    public function __construct(MediaItemRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return MediaItem::class;
    }

    protected function slugSource(): ?string
    {
        return null;
    }
}

<?php

namespace App\Managers;

use App\Models\MediaAlbum;
use App\Repositories\Contracts\MediaAlbumRepositoryInterface;

class MediaAlbumManager extends BaseContentManager
{
    public function __construct(MediaAlbumRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return MediaAlbum::class;
    }

    protected function slugSource(): ?string
    {
        return 'title';
    }
}

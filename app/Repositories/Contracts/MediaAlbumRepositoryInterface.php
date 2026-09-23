<?php

namespace App\Repositories\Contracts;

use App\Models\MediaAlbum;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends CrudRepositoryInterface<MediaAlbum>
 */
interface MediaAlbumRepositoryInterface extends CrudRepositoryInterface
{
    /** @return Collection<int, MediaAlbum> Альбомы с обложкой и числом материалов. */
    public function activeWithPreview(): Collection;

    public function findActiveBySlugWithItems(string $slug): MediaAlbum;
}

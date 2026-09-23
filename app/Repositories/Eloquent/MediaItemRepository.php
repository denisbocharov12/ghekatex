<?php

namespace App\Repositories\Eloquent;

use App\Models\MediaItem;
use App\Repositories\Contracts\MediaItemRepositoryInterface;

/**
 * @extends BaseRepository<MediaItem>
 */
class MediaItemRepository extends BaseRepository implements MediaItemRepositoryInterface
{
    protected function model(): string
    {
        return MediaItem::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['video_url'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'type', 'album_id'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'id', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }

    /** @return array<int, string> */
    protected function listRelations(): array
    {
        return ['album'];
    }
}

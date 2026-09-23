<?php

namespace App\Repositories\Eloquent;

use App\Models\MediaAlbum;
use App\Repositories\Contracts\MediaAlbumRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<MediaAlbum>
 */
class MediaAlbumRepository extends BaseRepository implements MediaAlbumRepositoryInterface
{
    protected function model(): string
    {
        return MediaAlbum::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['title', 'slug'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'slug', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }

    public function activeWithPreview(): Collection
    {
        return $this->query()
            ->active()
            ->withCount(['items' => fn (Builder $query) => $query->where('is_active', true)])
            // Первые материалы альбома идут на обложку и ленту превью
            ->with(['items' => fn ($relation) => $relation->where('is_active', true)->with('media')->limit(4)])
            ->ordered()
            ->get();
    }

    public function findActiveBySlugWithItems(string $slug): MediaAlbum
    {
        return $this->query()
            ->active()
            ->with(['items' => fn ($relation) => $relation->where('is_active', true)->with('media')])
            ->where('slug', $slug)
            ->firstOrFail();
    }
}

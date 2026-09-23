<?php

namespace App\Repositories\Eloquent;

use App\Models\Post;
use App\Repositories\Contracts\PostRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @extends BaseRepository<Post>
 */
class PostRepository extends BaseRepository implements PostRepositoryInterface
{
    protected function model(): string
    {
        return Post::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['title', 'slug'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'is_featured', 'type', 'category_id'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['published_at', 'created_at', 'views_count'];
    }

    protected function defaultSort(): string
    {
        return '-published_at';
    }

    /** @return array<int, string> */
    protected function listRelations(): array
    {
        return ['category', 'author', 'media'];
    }

    public function feed(array $filters, int $perPage = 9): LengthAwarePaginator
    {
        return $this->query()
            ->published()
            ->with(['category', 'media'])
            ->when($filters['type'] ?? null, fn (Builder $q, $type) => $q->where('type', $type))
            ->when($filters['category_id'] ?? null, fn (Builder $q, $id) => $q->where('category_id', $id))
            ->when($filters['search'] ?? null, fn (Builder $q, $term) => $q->where(
                fn (Builder $sub) => $this->applyLike($sub, (string) $term, ['title', 'excerpt']),
            ))
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function latestExcept(Post $post, int $limit = 3): Collection
    {
        return $this->query()
            ->published()
            ->with(['category', 'media'])
            ->whereKeyNot($post->getKey())
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function incrementViews(Post $post): void
    {
        // Счётчик просмотров не должен двигать updated_at и попадать в журнал действий
        $this->query()->whereKey($post->getKey())->update([
            'views_count' => DB::raw('views_count + 1'),
        ]);
    }
}

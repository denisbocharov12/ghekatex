<?php

namespace App\Repositories\Contracts;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends CrudRepositoryInterface<Post>
 */
interface PostRepositoryInterface extends CrudRepositoryInterface
{
    /**
     * Лента новостей с фильтрами по типу и рубрике.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Post>
     */
    public function feed(array $filters, int $perPage = 9): LengthAwarePaginator;

    /** @return Collection<int, Post> Свежие материалы, кроме текущего. */
    public function latestExcept(Post $post, int $limit = 3): Collection;

    /** Счётчик просмотров без обновления updated_at. */
    public function incrementViews(Post $post): void;
}

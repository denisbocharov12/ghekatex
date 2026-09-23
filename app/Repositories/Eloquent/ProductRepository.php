<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Product>
 */
class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    protected function model(): string
    {
        return Product::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['name', 'slug', 'article'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'is_featured', 'category_id'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'article', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }

    /** @return array<int, string> */
    protected function listRelations(): array
    {
        return ['category', 'media'];
    }

    public function featuredForHome(int $limit): Collection
    {
        return $this->query()
            ->active()
            ->featured()
            ->with(['category', 'media'])
            ->ordered()
            ->limit($limit)
            ->get();
    }

    public function catalog(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return $this->query()
            ->active()
            ->with(['category', 'media'])
            ->when($filters['category_id'] ?? null, fn (Builder $q, $id) => $q->where('category_id', $id))
            ->when($filters['fabric'] ?? null, fn (Builder $q, $slug) => $q->whereHas('fabrics', fn (Builder $sub) => $sub->where('slug', $slug)))
            ->when($filters['treatment'] ?? null, fn (Builder $q, $slug) => $q->whereHas('treatments', fn (Builder $sub) => $sub->where('slug', $slug)))
            ->when($filters['search'] ?? null, fn (Builder $q, $term) => $q->where(
                fn (Builder $sub) => $this->applyLike($sub, (string) $term, ['name', 'article', 'short_description']),
            ))
            ->ordered()
            ->paginate($perPage)
            ->withQueryString();
    }

    public function related(Product $product, int $limit = 4): Collection
    {
        return $this->query()
            ->active()
            ->with(['category', 'media'])
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->ordered()
            ->limit($limit)
            ->get();
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\ProductCategory;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<ProductCategory>
 */
class ProductCategoryRepository extends BaseRepository implements ProductCategoryRepositoryInterface
{
    protected function model(): string
    {
        return ProductCategory::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['name', 'slug'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'parent_id'];
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

    /** @return array<int, string> */
    protected function listRelations(): array
    {
        return ['parent'];
    }

    public function activeWithCounts(): Collection
    {
        return $this->query()
            ->active()
            ->with(['media'])
            ->withCount(['products' => fn (Builder $query) => $query->where('is_active', true)])
            ->ordered()
            ->get();
    }
}

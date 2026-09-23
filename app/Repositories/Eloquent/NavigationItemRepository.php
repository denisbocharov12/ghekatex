<?php

namespace App\Repositories\Eloquent;

use App\Models\NavigationItem;
use App\Repositories\Contracts\NavigationItemRepositoryInterface;

/**
 * @extends BaseRepository<NavigationItem>
 */
class NavigationItemRepository extends BaseRepository implements NavigationItemRepositoryInterface
{
    protected function model(): string
    {
        return NavigationItem::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['url', 'route_name'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'menu', 'parent_id'];
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
        return ['children'];
    }
}

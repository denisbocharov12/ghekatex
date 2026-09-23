<?php

namespace App\Repositories\Eloquent;

use App\Models\Faq;
use App\Repositories\Contracts\FaqRepositoryInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends BaseRepository<Faq>
 */
class FaqRepository extends BaseRepository implements FaqRepositoryInterface
{
    protected function model(): string
    {
        return Faq::class;
    }

    /** @return array<int, string> */
    protected function searchable(): array
    {
        return ['question', 'answer'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active', 'group'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'group', 'created_at'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }

    public function activeInGroup(string $group, ?int $limit = null): Collection
    {
        return $this->query()
            ->active()
            ->inGroup($group)
            ->ordered()
            ->when($limit !== null, fn (Builder $query) => $query->limit($limit))
            ->get();
    }
}

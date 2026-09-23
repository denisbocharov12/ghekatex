<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Concerns\SearchesLikeColumns;
use App\Repositories\Contracts\CrudRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * Общая реализация CRUD. Наследник объявляет модель и то, по чему
 * разрешено фильтровать и сортировать — остальное одинаково для всех разделов.
 *
 * @template TModel of Model
 *
 * @implements CrudRepositoryInterface<TModel>
 */
abstract class BaseRepository implements CrudRepositoryInterface
{
    use SearchesLikeColumns;

    /** @return class-string<TModel> */
    abstract protected function model(): string;

    /** Колонки быстрого поиска по списку админки. @return array<int, string> */
    protected function searchable(): array
    {
        return ['name'];
    }

    /** @return array<int, string> */
    protected function allowedSorts(): array
    {
        return ['sort_order', 'id', 'created_at'];
    }

    /** @return array<int, string> */
    protected function allowedFilters(): array
    {
        return ['is_active'];
    }

    protected function defaultSort(): string
    {
        return 'sort_order';
    }

    /** Связи, подгружаемые в список админки. @return array<int, string> */
    protected function listRelations(): array
    {
        return [];
    }

    /** @return Builder<TModel> */
    protected function query(): Builder
    {
        return $this->model()::query();
    }

    public function all(): Collection
    {
        $query = $this->query()->with($this->listRelations());

        if (method_exists($this->model(), 'scopeOrdered')) {
            return $query->ordered()->get();
        }

        // Сортировка задана в нотации query-builder: ведущий минус означает убывание
        $sort = $this->defaultSort();

        return $query->orderBy(ltrim($sort, '-'), str_starts_with($sort, '-') ? 'desc' : 'asc')->get();
    }

    public function paginate(int $perPage = 20, array $params = []): LengthAwarePaginator
    {
        $filters = array_map(
            static fn (string $field) => AllowedFilter::exact($field),
            $this->allowedFilters(),
        );

        // Поиск по нескольким колонкам сразу: ?filter[search]=...
        $filters[] = AllowedFilter::callback(
            'search',
            fn (Builder $query, mixed $value) => $query->where(
                fn (Builder $sub) => $this->applyLike($sub, (string) $value, $this->searchable()),
            ),
        );

        return QueryBuilder::for($this->query()->with($this->listRelations()), request())
            ->allowedFilters($filters)
            ->allowedSorts($this->allowedSorts())
            ->defaultSort($this->defaultSort())
            ->paginate($perPage)
            ->withQueryString();
    }

    public function find(int $id): Model
    {
        return $this->query()->findOrFail($id);
    }

    public function create(array $attributes): Model
    {
        return $this->model()::create($attributes);
    }

    public function update(Model $model, array $attributes): Model
    {
        $model->update($attributes);

        return $model->refresh();
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }

    public function count(): int
    {
        return $this->query()->count();
    }

    public function activeOrdered(?int $limit = null): Collection
    {
        $query = $this->query()->with($this->listRelations());

        $query = $this->applyVisibility($query);

        if (method_exists($this->model(), 'scopeOrdered')) {
            $query->ordered();
        } else {
            $sort = $this->defaultSort();
            $query->orderBy(ltrim($sort, '-'), str_starts_with($sort, '-') ? 'desc' : 'asc');
        }

        return $query->when($limit !== null, fn (Builder $q) => $q->limit($limit))->get();
    }

    public function findActiveBySlugOrFail(string $slug): Model
    {
        return $this->applyVisibility($this->query()->with($this->listRelations()))
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Видимость на витрине: посты отсекаются по дате публикации,
     * остальные разделы — по флагу активности. Служебные таблицы без
     * того и другого отдаются как есть.
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    protected function applyVisibility(Builder $query): Builder
    {
        $model = $this->model();

        if (method_exists($model, 'scopePublished')) {
            return $query->published();
        }

        return method_exists($model, 'scopeActive') ? $query->active() : $query;
    }

    public function reorder(array $rows): void
    {
        DB::transaction(function () use ($rows) {
            foreach ($rows as $row) {
                $this->query()
                    ->whereKey($row['id'])
                    ->update(['sort_order' => $row['sort_order']]);
            }
        });
    }
}

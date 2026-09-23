<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Общая часть контракта контентного репозитория.
 *
 * Специфика раздела (витринные выборки, подсказки поиска, счётчики дашборда)
 * объявляется в наследнике — здесь только то, что нужно админке всем разделам.
 *
 * @template TModel of Model
 */
interface CrudRepositoryInterface
{
    /** @return Collection<int, TModel> */
    public function all(): Collection;

    /**
     * Список админки: фильтры и сортировки разбирает spatie/query-builder.
     *
     * @param  array<string, mixed>  $params
     * @return LengthAwarePaginator<int, TModel>
     */
    public function paginate(int $perPage = 20, array $params = []): LengthAwarePaginator;

    /** @return TModel */
    public function find(int $id): Model;

    /** @param  array<string, mixed>  $attributes */
    public function create(array $attributes): Model;

    /**
     * @param  TModel  $model
     * @param  array<string, mixed>  $attributes
     * @return TModel
     */
    public function update(Model $model, array $attributes): Model;

    /** @param  TModel  $model */
    public function delete(Model $model): void;

    /** Всего записей — счётчики дашборда. */
    public function count(): int;

    /**
     * Витринная выборка: активные записи в заданном порядке.
     *
     * @return Collection<int, TModel>
     */
    public function activeOrdered(?int $limit = null): Collection;

    /**
     * Активная запись по слагу; 404, если её нет.
     *
     * @return TModel
     */
    public function findActiveBySlugOrFail(string $slug): Model;

    /**
     * Массовое переупорядочивание списка.
     *
     * @param  array<int, array{id: int, sort_order: int}>  $rows
     */
    public function reorder(array $rows): void;
}

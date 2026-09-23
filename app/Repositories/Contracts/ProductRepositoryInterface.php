<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends CrudRepositoryInterface<Product>
 */
interface ProductRepositoryInterface extends CrudRepositoryInterface
{
    /** @return Collection<int, Product> Избранные изделия для главной. */
    public function featuredForHome(int $limit): Collection;

    /**
     * Витрина каталога с фильтрами по категории, ткани и обработке.
     *
     * @param  array<string, mixed>  $filters
     * @return LengthAwarePaginator<int, Product>
     */
    public function catalog(array $filters, int $perPage = 12): LengthAwarePaginator;

    /** @return Collection<int, Product> Похожие изделия той же категории. */
    public function related(Product $product, int $limit = 4): Collection;
}

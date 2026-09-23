<?php

namespace App\Repositories\Contracts;

use App\Models\ProductCategory;
use Illuminate\Database\Eloquent\Collection;

/**
 * @extends CrudRepositoryInterface<ProductCategory>
 */
interface ProductCategoryRepositoryInterface extends CrudRepositoryInterface
{
    /** @return Collection<int, ProductCategory> Активные категории со счётчиком изделий. */
    public function activeWithCounts(): Collection;
}

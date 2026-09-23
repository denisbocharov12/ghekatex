<?php

namespace App\Managers;

use App\Models\ProductCategory;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;

class ProductCategoryManager extends BaseContentManager
{
    public function __construct(ProductCategoryRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return ProductCategory::class;
    }

    protected function slugSource(): ?string
    {
        return 'name';
    }
}

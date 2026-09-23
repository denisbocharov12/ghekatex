<?php

namespace App\Managers;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;

class ProductManager extends BaseContentManager
{
    public function __construct(ProductRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }

    protected function modelClass(): string
    {
        return Product::class;
    }

    protected function slugSource(): ?string
    {
        return 'name';
    }
}

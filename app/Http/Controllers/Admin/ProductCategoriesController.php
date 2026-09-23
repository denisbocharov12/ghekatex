<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\ProductCategoryRequest;
use App\Managers\ProductCategoryManager;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;

class ProductCategoriesController extends AdminResourceController
{
    public function __construct(ProductCategoryManager $manager, ProductCategoryRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'ProductCategories';
    }

    protected function routePrefix(): string
    {
        return 'product-categories';
    }

    protected function requestClass(): string
    {
        return ProductCategoryRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'description'],
            plain: ['slug', 'parent_id', 'sort_order', 'is_active'],
            casts: ['parent_id' => 'int', 'sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['cover'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        // Категория не может быть родителем сама себе — фильтруем на клиенте по id
        return ['parents' => $this->repository->all()];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\ProductRequest;
use App\Managers\ProductManager;
use App\Repositories\Contracts\FabricRepositoryInterface;
use App\Repositories\Contracts\ProductCategoryRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Repositories\Contracts\TreatmentRepositoryInterface;

class ProductsController extends AdminResourceController
{
    public function __construct(ProductManager $manager, ProductRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Products';
    }

    protected function routePrefix(): string
    {
        return 'products';
    }

    protected function requestClass(): string
    {
        return ProductRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'short_description', 'description', 'composition'],
            plain: ['slug', 'article', 'category_id', 'min_order_quantity', 'lead_time_days', 'is_featured', 'sort_order', 'is_active'],
            casts: ['category_id' => 'int', 'min_order_quantity' => 'int', 'lead_time_days' => 'int', 'is_featured' => 'bool', 'sort_order' => 'int', 'is_active' => 'bool'],
            rows: [
                'attributes' => ['translatable' => ['label', 'value'], 'required' => 'label'],
            ],
            singleMedia: ['cover'],
            relations: ['fabrics', 'treatments'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [
            'categories' => app(ProductCategoryRepositoryInterface::class)->all(),
            'fabrics' => app(FabricRepositoryInterface::class)->all(),
            'treatments' => app(TreatmentRepositoryInterface::class)->all(),
        ];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

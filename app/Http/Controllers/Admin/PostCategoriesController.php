<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\PostCategoryRequest;
use App\Managers\PostCategoryManager;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;

class PostCategoriesController extends AdminResourceController
{
    public function __construct(PostCategoryManager $manager, PostCategoryRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'PostCategories';
    }

    protected function routePrefix(): string
    {
        return 'post-categories';
    }

    protected function requestClass(): string
    {
        return PostCategoryRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'description'],
            plain: ['slug', 'sort_order', 'is_active'],
            casts: ['sort_order' => 'int', 'is_active' => 'bool'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

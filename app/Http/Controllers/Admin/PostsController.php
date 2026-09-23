<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\PostRequest;
use App\Managers\PostManager;
use App\Models\User;
use App\Repositories\Contracts\PostCategoryRepositoryInterface;
use App\Repositories\Contracts\PostRepositoryInterface;

class PostsController extends AdminResourceController
{
    public function __construct(PostManager $manager, PostRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Posts';
    }

    protected function routePrefix(): string
    {
        return 'posts';
    }

    protected function requestClass(): string
    {
        return PostRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['title', 'excerpt', 'body'],
            plain: ['slug', 'type', 'category_id', 'author_id', 'published_at', 'is_featured', 'reading_minutes', 'is_active'],
            casts: ['category_id' => 'int', 'author_id' => 'int', 'published_at' => 'date', 'is_featured' => 'bool', 'reading_minutes' => 'int', 'is_active' => 'bool'],
            singleMedia: ['cover'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [
            'categories' => app(PostCategoryRepositoryInterface::class)->all(),
            'authors' => User::query()->active()->orderBy('name')->get(['id', 'name']),
            'types' => ['news', 'article', 'review'],
        ];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

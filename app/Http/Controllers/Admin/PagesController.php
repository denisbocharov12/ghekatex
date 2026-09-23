<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Enums\PageTemplate;
use App\Http\Requests\Admin\PageRequest;
use App\Managers\PageManager;
use App\Repositories\Contracts\PageRepositoryInterface;

class PagesController extends AdminResourceController
{
    public function __construct(PageManager $manager, PageRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Pages';
    }

    protected function routePrefix(): string
    {
        return 'pages';
    }

    protected function requestClass(): string
    {
        return PageRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['title', 'subtitle', 'body'],
            plain: ['slug', 'template', 'sort_order', 'is_active'],
            casts: ['sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['cover'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [
            'templates' => array_map(
                static fn (PageTemplate $template) => ['value' => $template->value, 'label' => $template->label()],
                PageTemplate::cases(),
            ),
        ];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

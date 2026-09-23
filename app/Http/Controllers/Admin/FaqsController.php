<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Enums\FaqGroup;
use App\Http\Requests\Admin\FaqRequest;
use App\Managers\FaqManager;
use App\Repositories\Contracts\FaqRepositoryInterface;

class FaqsController extends AdminResourceController
{
    public function __construct(FaqManager $manager, FaqRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Faqs';
    }

    protected function routePrefix(): string
    {
        return 'faqs';
    }

    protected function requestClass(): string
    {
        return FaqRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['question', 'answer'],
            plain: ['group', 'sort_order', 'is_active'],
            casts: ['sort_order' => 'int', 'is_active' => 'bool'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [
            'groups' => array_map(
                static fn (FaqGroup $group) => ['value' => $group->value, 'label' => $group->label()],
                FaqGroup::cases(),
            ),
        ];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

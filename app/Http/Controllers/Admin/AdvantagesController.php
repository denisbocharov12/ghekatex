<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\AdvantageRequest;
use App\Managers\AdvantageManager;
use App\Repositories\Contracts\AdvantageRepositoryInterface;

class AdvantagesController extends AdminResourceController
{
    public function __construct(AdvantageManager $manager, AdvantageRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Advantages';
    }

    protected function routePrefix(): string
    {
        return 'advantages';
    }

    protected function requestClass(): string
    {
        return AdvantageRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['title', 'description', 'value_suffix'],
            plain: ['icon', 'value', 'is_counter', 'sort_order', 'is_active'],
            casts: ['is_counter' => 'bool', 'sort_order' => 'int', 'is_active' => 'bool'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

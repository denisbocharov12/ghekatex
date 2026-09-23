<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\TreatmentRequest;
use App\Managers\TreatmentManager;
use App\Repositories\Contracts\TreatmentRepositoryInterface;

class TreatmentsController extends AdminResourceController
{
    public function __construct(TreatmentManager $manager, TreatmentRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Treatments';
    }

    protected function routePrefix(): string
    {
        return 'treatments';
    }

    protected function requestClass(): string
    {
        return TreatmentRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'description'],
            plain: ['slug', 'icon', 'sort_order', 'is_active'],
            casts: ['sort_order' => 'int', 'is_active' => 'bool'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

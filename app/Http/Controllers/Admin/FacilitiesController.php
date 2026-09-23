<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\FacilityRequest;
use App\Managers\FacilityManager;
use App\Repositories\Contracts\FacilityRepositoryInterface;

class FacilitiesController extends AdminResourceController
{
    public function __construct(FacilityManager $manager, FacilityRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Facilities';
    }

    protected function routePrefix(): string
    {
        return 'facilities';
    }

    protected function requestClass(): string
    {
        return FacilityRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'summary', 'description'],
            plain: ['slug', 'icon', 'capacity_per_month', 'employees_count', 'sort_order', 'is_active'],
            casts: ['capacity_per_month' => 'int', 'employees_count' => 'int', 'sort_order' => 'int', 'is_active' => 'bool'],
            rows: [
                'specs' => ['translatable' => ['label', 'value'], 'required' => 'label'],
            ],
            singleMedia: ['cover'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

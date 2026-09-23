<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Enums\OfficeType;
use App\Http\Requests\Admin\OfficeRequest;
use App\Managers\OfficeManager;
use App\Repositories\Contracts\OfficeRepositoryInterface;

class OfficesController extends AdminResourceController
{
    public function __construct(OfficeManager $manager, OfficeRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Offices';
    }

    protected function routePrefix(): string
    {
        return 'offices';
    }

    protected function requestClass(): string
    {
        return OfficeRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'address', 'city', 'working_hours'],
            plain: ['type', 'country_code', 'postal_code', 'phones', 'emails', 'latitude', 'longitude', 'is_primary', 'sort_order', 'is_active'],
            casts: ['phones' => 'list', 'emails' => 'list', 'latitude' => 'float', 'longitude' => 'float', 'is_primary' => 'bool', 'sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['photo'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [
            'types' => array_map(
                static fn (OfficeType $type) => ['value' => $type->value, 'label' => $type->label()],
                OfficeType::cases(),
            ),
        ];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

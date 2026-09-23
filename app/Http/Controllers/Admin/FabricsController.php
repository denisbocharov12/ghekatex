<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\FabricRequest;
use App\Managers\FabricManager;
use App\Repositories\Contracts\FabricRepositoryInterface;

class FabricsController extends AdminResourceController
{
    public function __construct(FabricManager $manager, FabricRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Fabrics';
    }

    protected function routePrefix(): string
    {
        return 'fabrics';
    }

    protected function requestClass(): string
    {
        return FabricRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'description', 'composition'],
            plain: ['slug', 'weight_gsm', 'color_hex', 'sort_order', 'is_active'],
            casts: ['weight_gsm' => 'int', 'sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['swatch'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

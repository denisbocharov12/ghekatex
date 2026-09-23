<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\PartnerRequest;
use App\Managers\PartnerManager;
use App\Repositories\Contracts\PartnerRepositoryInterface;

class PartnersController extends AdminResourceController
{
    public function __construct(PartnerManager $manager, PartnerRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Partners';
    }

    protected function routePrefix(): string
    {
        return 'partners';
    }

    protected function requestClass(): string
    {
        return PartnerRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['description'],
            plain: ['name', 'website_url', 'country_code', 'is_featured', 'sort_order', 'is_active'],
            casts: ['is_featured' => 'bool', 'sort_order' => 'int', 'is_active' => 'bool'],
            singleMedia: ['logo'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

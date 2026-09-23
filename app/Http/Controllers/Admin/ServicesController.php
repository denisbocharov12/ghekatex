<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\ServiceRequest;
use App\Managers\ServiceManager;
use App\Models\Service;
use App\Repositories\Contracts\ServiceRepositoryInterface;

class ServicesController extends AdminResourceController
{
    public function __construct(ServiceManager $manager, ServiceRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Services';
    }

    protected function routePrefix(): string
    {
        return 'services';
    }

    protected function requestClass(): string
    {
        return ServiceRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['name', 'short_description', 'description', 'lead_time'],
            plain: ['slug', 'icon', 'is_featured', 'sort_order', 'is_active'],
            casts: ['is_featured' => 'bool', 'sort_order' => 'int', 'is_active' => 'bool'],
            rows: [
                'highlights' => ['translatable' => ['label', 'value'], 'plain' => ['icon'], 'required' => 'label'],
                'process_steps' => ['translatable' => ['title', 'text'], 'required' => 'title'],
            ],
            singleMedia: ['cover'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return ['highlightIcons' => Service::HIGHLIGHT_ICONS];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

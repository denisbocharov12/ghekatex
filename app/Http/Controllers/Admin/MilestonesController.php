<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Http\Requests\Admin\CompanyMilestoneRequest;
use App\Managers\CompanyMilestoneManager;
use App\Repositories\Contracts\CompanyMilestoneRepositoryInterface;

class MilestonesController extends AdminResourceController
{
    public function __construct(CompanyMilestoneManager $manager, CompanyMilestoneRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Milestones';
    }

    protected function routePrefix(): string
    {
        return 'milestones';
    }

    protected function requestClass(): string
    {
        return CompanyMilestoneRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['title', 'description'],
            plain: ['year', 'sort_order', 'is_active'],
            casts: ['sort_order' => 'int', 'is_active' => 'bool'],
        );
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

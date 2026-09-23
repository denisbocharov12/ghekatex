<?php

namespace App\Http\Controllers\Admin;

use App\Data\Schemas\FieldSchema;
use App\Enums\NavigationMenu;
use App\Http\Requests\Admin\NavigationItemRequest;
use App\Managers\NavigationItemManager;
use App\Repositories\Contracts\NavigationItemRepositoryInterface;

class NavigationController extends AdminResourceController
{
    public function __construct(NavigationItemManager $manager, NavigationItemRepositoryInterface $repository)
    {
        parent::__construct($manager, $repository);
    }

    protected function pagePrefix(): string
    {
        return 'Navigation';
    }

    protected function routePrefix(): string
    {
        return 'navigation';
    }

    protected function requestClass(): string
    {
        return NavigationItemRequest::class;
    }

    protected function schema(): FieldSchema
    {
        return new FieldSchema(
            translatable: ['label'],
            plain: ['menu', 'parent_id', 'route_name', 'url', 'icon', 'opens_in_new_tab', 'sort_order', 'is_active'],
            casts: ['parent_id' => 'int', 'opens_in_new_tab' => 'bool', 'sort_order' => 'int', 'is_active' => 'bool'],
        );
    }

    /** @return array<string, mixed> */
    protected function formOptions(): array
    {
        return [
            'menus' => array_map(
                static fn (NavigationMenu $menu) => ['value' => $menu->value, 'label' => $menu->label()],
                NavigationMenu::cases(),
            ),
            'parents' => $this->repository->all(),
            'routes' => [
                'home', 'about', 'catalog.index', 'services.index',
                'news.index', 'media.index', 'contacts.index',
            ],
        ];
    }

    /** @return array<string, mixed> */
    protected function indexProps(): array
    {
        return [];
    }
}

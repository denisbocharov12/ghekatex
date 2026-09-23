<?php

namespace Database\Seeders;

use App\Enums\PermissionAction;
use App\Enums\PermissionArea;
use App\Enums\RoleName;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (PermissionArea::allPermissions() as $name) {
            Permission::findOrCreate($name, 'web');
        }

        $roles = [
            // Супер-администратор обходит проверки через Gate::before, права ему не нужны
            RoleName::SuperAdmin->value => [],
            RoleName::Admin->value => PermissionArea::allPermissions(),
            RoleName::ContentManager->value => $this->areas([
                PermissionArea::Dashboard,
                PermissionArea::Pages,
                PermissionArea::Posts,
                PermissionArea::PostCategories,
                PermissionArea::Media,
                PermissionArea::Products,
                PermissionArea::ProductCategories,
                PermissionArea::Fabrics,
                PermissionArea::Treatments,
                PermissionArea::Services,
                PermissionArea::Certificates,
                PermissionArea::Partners,
                PermissionArea::Facilities,
                PermissionArea::Milestones,
                PermissionArea::Advantages,
                PermissionArea::HeroSlides,
                PermissionArea::Seo,
                PermissionArea::Translations,
                PermissionArea::Navigation,
            ]),
            RoleName::Sales->value => array_merge(
                $this->areas([PermissionArea::Dashboard, PermissionArea::Requests, PermissionArea::Subscribers]),
                ['offices.view', 'products.view', 'services.view'],
            ),
            RoleName::Viewer->value => array_map(
                static fn (PermissionArea $area) => $area->value.'.'.PermissionAction::View->value,
                array_filter(
                    PermissionArea::cases(),
                    static fn (PermissionArea $area) => in_array(PermissionAction::View, $area->actions(), true),
                ),
            ),
        ];

        foreach ($roles as $name => $permissions) {
            Role::findOrCreate($name, 'web')->syncPermissions($permissions);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @param  array<int, PermissionArea>  $areas
     * @return array<int, string>
     */
    private function areas(array $areas): array
    {
        return array_merge(...array_map(
            static fn (PermissionArea $area) => $area->permissions(),
            $areas,
        ));
    }
}

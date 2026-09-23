<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionArea;
use App\Enums\RoleName;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RolesController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Roles/Index', [
            'roles' => Role::query()->with('permissions')->orderBy('name')->get()
                ->map(fn (Role $role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'system' => in_array($role->name, RoleName::values(), true),
                    'permissions' => $role->permissions->pluck('name'),
                ]),
            'areas' => array_map(
                static fn (PermissionArea $area) => [
                    'area' => $area->value,
                    'label' => $area->label(),
                    'permissions' => $area->permissions(),
                ],
                PermissionArea::cases(),
            ),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        Role::create(['name' => $validated['name']])
            ->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', __('Роль создана'));
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:60', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['string', 'exists:permissions,name'],
        ]);

        // Системную роль можно перенастроить, но не переименовать
        if (! in_array($role->name, RoleName::values(), true)) {
            $role->update(['name' => $validated['name']]);
        }

        $role->syncPermissions($validated['permissions'] ?? []);

        return back()->with('success', __('Права обновлены'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        abort_if(in_array($role->name, RoleName::values(), true), 403, __('Системную роль удалить нельзя.'));

        $role->delete();

        return back()->with('success', __('Роль удалена'));
    }
}

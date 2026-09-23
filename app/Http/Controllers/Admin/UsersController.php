<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PermissionArea;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
{
    public function __construct(
        private readonly UserRepositoryInterface $users,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Users/Index', [
            'items' => $this->users->paginate((int) $request->integer('per_page', 20) ?: 20),
            'filters' => $request->only(['filter', 'sort', 'page']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Form', $this->formProps());
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request, null);

        $user = $this->users->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
            'locale' => $validated['locale'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        $user->syncRoles($validated['roles'] ?? []);

        return redirect()->route('admin.users.index')->with('success', __('Пользователь создан'));
    }

    public function edit(int $item): Response
    {
        $user = $this->users->find($item);

        return Inertia::render('Users/Form', array_merge($this->formProps(), [
            'item' => array_merge($user->toArray(), [
                'roles' => $user->getRoleNames(),
                'avatar' => $user->avatarUrl(),
            ]),
        ]));
    }

    public function update(Request $request, int $item): RedirectResponse
    {
        $user = $this->users->find($item);
        $validated = $this->validateUser($request, $user);

        $attributes = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'position' => $validated['position'] ?? null,
            'locale' => $validated['locale'],
            'is_active' => $validated['is_active'] ?? true,
        ];

        // Пустое поле пароля означает «оставить прежний»
        if (! empty($validated['password'])) {
            $attributes['password'] = $validated['password'];
        }

        $this->users->update($user, $attributes);
        $user->syncRoles($validated['roles'] ?? []);

        return back()->with('success', __('Изменения сохранены'));
    }

    public function destroy(Request $request, int $item): RedirectResponse
    {
        $user = $this->users->find($item);

        abort_if($user->is($request->user()), 403, __('Нельзя удалить собственную учётную запись.'));

        $this->users->delete($user);

        return redirect()->route('admin.users.index')->with('success', __('Пользователь удалён'));
    }

    /** @return array<string, mixed> */
    private function formProps(): array
    {
        return [
            'roles' => Role::query()->orderBy('name')->pluck('name'),
            'permissions' => array_map(
                static fn (PermissionArea $area) => [
                    'area' => $area->value,
                    'label' => $area->label(),
                    'permissions' => $area->permissions(),
                ],
                PermissionArea::cases(),
            ),
            'localeCodes' => config('ghekatex.locales.available'),
        ];
    }

    /** @return array<string, mixed> */
    private function validateUser(Request $request, ?User $user): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user?->id)],
            'password' => [$user === null ? 'required' : 'nullable', 'confirmed', Password::min(10)->letters()->numbers()],
            'phone' => ['nullable', 'string', 'max:40'],
            'position' => ['nullable', 'string', 'max:120'],
            'locale' => ['required', 'string', 'in:'.implode(',', config('ghekatex.locales.available'))],
            'is_active' => ['boolean'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['string', 'exists:roles,name'],
        ]);
    }
}

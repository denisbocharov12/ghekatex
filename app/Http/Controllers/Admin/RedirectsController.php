<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect as RedirectRule;
use App\Repositories\Contracts\RedirectRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

/** Постоянные переадресации: переезд страниц без потери позиций в поиске. */
class RedirectsController extends Controller
{
    public function __construct(
        private readonly RedirectRepositoryInterface $redirects,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Redirects/Index', [
            'items' => $this->redirects->paginate((int) $request->integer('per_page', 30) ?: 30),
            'filters' => $request->only(['filter', 'sort', 'page']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $this->redirects->create($this->validated($request, null));

        return back()->with('success', __('Редирект добавлен'));
    }

    public function update(Request $request, int $item): RedirectResponse
    {
        $model = $this->redirects->find($item);
        $this->redirects->update($model, $this->validated($request, $model));

        return back()->with('success', __('Редирект обновлён'));
    }

    public function destroy(int $item): RedirectResponse
    {
        $this->redirects->delete($this->redirects->find($item));

        return back()->with('success', __('Редирект удалён'));
    }

    /** @return array<string, mixed> */
    private function validated(Request $request, ?RedirectRule $model): array
    {
        return $request->validate([
            'from_path' => ['required', 'string', 'max:255', Rule::unique('redirects', 'from_path')->ignore($model?->id)],
            'to_path' => ['required', 'string', 'max:255'],
            'status_code' => ['required', 'integer', 'in:301,302,307,308'],
            'is_active' => ['boolean'],
        ]);
    }
}

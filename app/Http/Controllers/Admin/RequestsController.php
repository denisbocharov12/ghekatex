<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ContactRequestSource;
use App\Enums\ContactRequestStatus;
use App\Http\Controllers\Controller;
use App\Managers\ContactRequestManager;
use App\Repositories\Contracts\ContactRequestRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RequestsController extends Controller
{
    public function __construct(
        private readonly ContactRequestRepositoryInterface $requests,
        private readonly ContactRequestManager $manager,
    ) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Requests/Index', [
            'items' => $this->requests->paginate((int) $request->integer('per_page', 20) ?: 20),
            'statuses' => array_map(
                static fn (ContactRequestStatus $status) => [
                    'value' => $status->value,
                    'label' => $status->label(),
                    'tone' => $status->tone(),
                ],
                ContactRequestStatus::cases(),
            ),
            'sources' => ContactRequestSource::values(),
            'filters' => $request->only(['filter', 'sort', 'page']),
        ]);
    }

    public function show(int $item): Response
    {
        $model = $this->requests->find($item);
        $model->load('handler');

        return Inertia::render('Requests/Show', [
            'item' => $model,
            'statuses' => array_map(
                static fn (ContactRequestStatus $status) => ['value' => $status->value, 'label' => $status->label()],
                ContactRequestStatus::cases(),
            ),
        ]);
    }

    public function update(Request $request, int $item): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:'.implode(',', ContactRequestStatus::values())],
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->manager->updateStatus(
            $this->requests->find($item),
            ContactRequestStatus::from($validated['status']),
            $validated['admin_note'] ?? null,
            $request->user()?->id,
        );

        return back()->with('success', __('Заявка обновлена'));
    }

    public function destroy(int $item): RedirectResponse
    {
        $this->manager->delete($this->requests->find($item));

        return redirect()->route('admin.requests.index')->with('success', __('Заявка удалена'));
    }
}

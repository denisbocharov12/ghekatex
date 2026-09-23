<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SettingGroup;
use App\Http\Controllers\Controller;
use App\Managers\SettingManager;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Настройки сайта. Ключи создаются сидером, редактор меняет только значения:
 * так витрина не остаётся без обязательной настройки.
 */
class SettingsController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
        private readonly SettingManager $manager,
    ) {}

    public function index(): Response
    {
        return Inertia::render('Settings/Index', [
            'settings' => $this->settings->editable(),
            'groups' => array_map(
                static fn (SettingGroup $group) => ['value' => $group->value, 'label' => $group->label()],
                SettingGroup::cases(),
            ),
            'localeCodes' => config('ghekatex.locales.available'),
            'localeLabels' => config('ghekatex.locales.labels'),
            'defaultLocale' => config('ghekatex.locales.default'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $payload = $request->validate([
            'values' => ['required', 'array'],
            'values.*' => ['nullable'],
            'files' => ['nullable', 'array'],
            'files.*' => ['nullable', 'file', 'max:8192'],
        ]);

        $this->manager->saveMany($payload['values'], $request->file('files', []));

        return back()->with('success', __('Настройки сохранены'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

/** Личные данные и язык панели текущего пользователя. */
class ProfileController extends Controller
{
    public function edit(Request $request): Response
    {
        $user = $request->user();

        return Inertia::render('Profile', [
            'item' => array_merge($user->toArray(), ['avatar' => $user->avatarUrl()]),
            'localeCodes' => config('ghekatex.locales.available'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:180', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:40'],
            'position' => ['nullable', 'string', 'max:120'],
            'locale' => ['required', 'string', 'in:'.implode(',', config('ghekatex.locales.available'))],
            'password' => ['nullable', 'confirmed', Password::min(10)->letters()->numbers()],
            'avatar' => ['nullable', 'image', 'max:4096'],
        ]);

        $attributes = collect($validated)->except(['password', 'avatar'])->all();

        if (! empty($validated['password'])) {
            $attributes['password'] = $validated['password'];
        }

        $user->update($attributes);

        if ($request->hasFile('avatar')) {
            $user->addMediaFromRequest('avatar')->toMediaCollection('avatar');
        }

        return back()->with('success', __('Профиль обновлён'));
    }
}

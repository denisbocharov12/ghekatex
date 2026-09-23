<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Подписка на новости компании. Адрес подтверждается сразу: рассылка
 * ведётся вручную, двойное подтверждение добавим вместе с провайдером рассылок.
 */
class SubscriptionController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email:rfc', 'max:180'],
            'consent' => ['accepted'],
        ]);

        Subscriber::query()->updateOrCreate(
            ['email' => mb_strtolower($validated['email'])],
            [
                'locale' => app()->getLocale(),
                'token' => Str::random(48),
                'confirmed_at' => now(),
                'unsubscribed_at' => null,
            ],
        );

        return back()->with('success', __('Спасибо за подписку!'));
    }

    public function destroy(string $locale, string $token): RedirectResponse
    {
        Subscriber::query()
            ->where('token', $token)
            ->update(['unsubscribed_at' => now()]);

        return redirect()
            ->route('home', ['locale' => $locale])
            ->with('success', __('Вы отписаны от рассылки.'));
    }
}

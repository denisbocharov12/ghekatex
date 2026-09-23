<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\ConsentLogRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Фиксация согласия на cookie.
 *
 * Сам выбор хранится в cookie на стороне браузера — сервер ведёт журнал,
 * чтобы иметь доказательство согласия, как того требует GDPR.
 */
class ConsentController extends Controller
{
    public function __invoke(Request $request, ConsentLogRepositoryInterface $logs): JsonResponse
    {
        $allowed = config('ghekatex.consent.categories');

        $validated = $request->validate([
            'categories' => ['required', 'array'],
            'categories.*' => ['string', 'in:'.implode(',', $allowed)],
            'anonymous_id' => ['nullable', 'uuid'],
        ]);

        $categories = array_values(array_unique(array_merge(['necessary'], $validated['categories'])));
        $anonymousId = $validated['anonymous_id'] ?? (string) Str::uuid();

        $logs->create([
            'anonymous_id' => $anonymousId,
            'ip_hash' => hash('sha256', (string) $request->ip().config('app.key')),
            'categories' => $categories,
            'policy_version' => config('ghekatex.consent.policy_version'),
            'locale' => app()->getLocale(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'created_at' => now(),
        ]);

        return response()->json([
            'anonymous_id' => $anonymousId,
            'categories' => $categories,
        ]);
    }
}

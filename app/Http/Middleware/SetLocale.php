<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Определяет язык запроса.
 *
 * Приоритет: сегмент пути → cookie → заголовок Accept-Language → язык по умолчанию.
 * Для панели управления берётся язык интерфейса пользователя: редактор может
 * вести румынскую версию сайта, оставаясь в русскоязычной админке.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $available = config('ghekatex.locales.available');
        $default = config('ghekatex.locales.default');
        $cookieName = config('ghekatex.locales.cookie');

        if ($request->is('admin', 'admin/*')) {
            $locale = $request->user()?->locale;

            app()->setLocale(in_array($locale, $available, true) ? $locale : 'ru');

            return $next($request);
        }

        $locale = $request->segment(1);

        if (! in_array($locale, $available, true)) {
            $locale = $request->cookie($cookieName);
        }

        if (! in_array($locale, $available, true)) {
            $locale = $request->getPreferredLanguage(array_map(
                static fn (string $item) => $item,
                $available,
            ));
        }

        $locale = in_array($locale, $available, true) ? $locale : $default;

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        // Маршруты витрины принимают {locale} первым параметром — подставляем его по умолчанию
        URL::defaults(['locale' => $locale]);

        $response = $next($request);

        // Запоминаем выбор на полгода, чтобы не переспрашивать язык на каждом входе
        if ($request->cookie($cookieName) !== $locale) {
            $response->headers->setCookie(
                Cookie::make($cookieName, $locale, 60 * 24 * 180, '/', null, $request->isSecure(), false, false, 'lax'),
            );
        }

        return $response;
    }
}

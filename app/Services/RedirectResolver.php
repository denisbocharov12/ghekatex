<?php

namespace App\Services;

use App\Models\Page;
use App\Models\Redirect;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Throwable;

/**
 * Переадресации, настроенные в админке.
 *
 * Правило проверяется только тогда, когда маршрут не найден: посредник
 * группы `web` для несуществующего адреса вообще не запускается, поэтому
 * подключаемся к обработчику 404. Список кешируется — иначе каждый промах
 * по адресу превращался бы в поход в базу.
 */
class RedirectResolver
{
    private const CACHE_KEY = 'ghekatex.redirects';

    public function resolve(Request $request): ?RedirectResponse
    {
        if (! $request->isMethod('GET')) {
            return null;
        }

        $path = '/'.trim($request->path(), '/');
        $rules = $this->rules();

        if (! isset($rules[$path])) {
            return $this->withLocale($request, $path);
        }

        $rule = $rules[$path];

        Redirect::query()->whereKey($rule['id'])->update([
            'hits_count' => $rule['hits'] + 1,
            'last_hit_at' => now(),
        ]);

        Cache::forget(self::CACHE_KEY);

        return redirect()->to($rule['to'], $rule['code']);
    }

    /**
     * Адрес без языкового сегмента.
     *
     * Ссылки вида `/catalog/midi-wrap-dress` приходят из писем, каталогов
     * партнёров и старых закладок. Если тот же путь существует под языком,
     * отправляем туда, а не показываем 404.
     */
    private function withLocale(Request $request, string $path): ?RedirectResponse
    {
        $available = (array) config('ghekatex.locales.available');
        $segment = $request->segment(1);

        if ($segment === null || in_array($segment, $available, true)) {
            return null;
        }

        $cookie = (string) $request->cookie(config('ghekatex.locales.cookie', 'locale'));
        $locale = in_array($cookie, $available, true) ? $cookie : config('ghekatex.locales.default');

        $target = '/'.$locale.$path;

        try {
            $route = Route::getRoutes()->match(Request::create($target, 'GET'));
        } catch (Throwable) {
            return null;
        }

        // Замыкающий маршрут страниц ловит любой слаг — иначе получили бы
        // переадресацию на тот же 404, только с лишним переходом
        if ($route->getName() === 'pages.show'
            && ! Page::query()->where('slug', $route->parameter('page'))->exists()) {
            return null;
        }

        return redirect()->to($target, 301);
    }

    /** @return array<string, array{id: int, to: string, code: int, hits: int}> */
    private function rules(): array
    {
        return Cache::remember(
            self::CACHE_KEY,
            now()->addHour(),
            fn () => Redirect::query()
                ->active()
                ->get(['id', 'from_path', 'to_path', 'status_code', 'hits_count'])
                ->mapWithKeys(fn (Redirect $rule) => [
                    '/'.trim($rule->from_path, '/') => [
                        'id' => $rule->id,
                        'to' => $rule->to_path,
                        'code' => $rule->status_code,
                        'hits' => $rule->hits_count,
                    ],
                ])
                ->all(),
        );
    }
}

<?php

use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\SetLocale;
use App\Services\RedirectResolver;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleMiddleware;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function (): void {
            // Панель управления живёт на собственном префиксе и вне языковых сегментов
            Route::middleware('web')
                ->prefix('admin')
                ->name('admin.')
                ->group(base_path('routes/admin.php'));
        },
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            SetLocale::class,
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
        ]);

        // Неавторизованного отправляем на вход в панель: маршрута login у витрины нет
        $middleware->redirectGuestsTo(fn () => route('admin.login'));

        $middleware->alias([
            'role' => RoleMiddleware::class,
            'permission' => PermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Прежде чем отдать 404, проверяем таблицу редиректов из админки
        $exceptions->render(function (NotFoundHttpException $exception, Request $request) {
            return app(RedirectResolver::class)->resolve($request);
        });

        /*
        | Фирменная страница ошибки вместо стандартной заглушки Laravel.
        |
        | Отдаётся из обработчика исключений, куда посредники уже не доходят,
        | поэтому язык и ссылка на главную передаются пропсами. API и запросы
        | панели оставляем как есть: им нужен JSON и обычные редиректы.
        */
        $exceptions->respond(function (SymfonyResponse $response, Throwable $exception, Request $request) {
            $status = $response->getStatusCode();

            if ($request->expectsJson() || $request->is('api/*') || $request->is('admin', 'admin/*')) {
                return $response;
            }

            if (! in_array($status, [403, 404, 419, 429, 500, 503], true)) {
                return $response;
            }

            // Локально при включённом debug полезнее подробная трассировка
            if (config('app.debug') && $status >= 500) {
                return $response;
            }

            $available = config('ghekatex.locales.available');
            $segment = $request->segment(1);
            $locale = in_array($segment, $available, true) ? $segment : config('ghekatex.locales.default');

            return Inertia::render('Error', [
                'status' => $status,
                'locale' => $locale,
                'home' => url('/'.$locale),
            ])
                ->toResponse($request)
                ->setStatusCode($status);
        });
    })->create();

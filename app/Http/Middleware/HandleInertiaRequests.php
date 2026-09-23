<?php

namespace App\Http\Middleware;

use App\Repositories\Contracts\ServiceRepositoryInterface;
use App\Services\NavigationService;
use App\Services\SettingService;
use App\Services\TranslationCatalogService;
use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function __construct(
        private readonly SettingService $settings,
        private readonly NavigationService $navigation,
        private readonly TranslationCatalogService $translations,
        private readonly ServiceRepositoryInterface $services,
    ) {}

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Пропсы, доступные на каждой странице.
     *
     * Панель и витрина получают разные наборы: админке не нужны меню сайта,
     * витрине — список прав пользователя.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $locale = app()->getLocale();
        $isAdmin = $request->is('admin', 'admin/*');

        return array_merge(parent::share($request), [
            'locale' => $locale,
            'locales' => [
                'available' => config('ghekatex.locales.available'),
                'labels' => config('ghekatex.locales.labels'),
                'default' => config('ghekatex.locales.default'),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => fn () => $request->user() === null ? null : [
                    'id' => $request->user()->id,
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'position' => $request->user()->position,
                    'locale' => $request->user()->locale,
                    'avatar' => $request->user()->avatarUrl(),
                    'roles' => $request->user()->getRoleNames(),
                    'permissions' => $request->user()->getAllPermissions()->pluck('name'),
                ],
            ],
            'i18n' => fn () => $this->translations->overrides($locale, $isAdmin ? 'admin' : 'site'),
            'site' => $isAdmin ? null : fn () => [
                'menus' => $this->navigation->menus($locale),
                'general' => $this->settings->group('general', $locale),
                'contacts' => $this->settings->group('contacts', $locale),
                'social' => $this->settings->group('social', $locale),
                // Перечень услуг нужен модалке обратной связи на любой странице
                'services' => $this->services->activeOrdered()
                    ->map(static fn ($service): array => [
                        'slug' => $service->slug,
                        'name' => $service->getTranslation('name', $locale),
                    ])
                    ->values()
                    ->all(),
            ],
            'consent' => $isAdmin ? null : [
                'cookie' => config('ghekatex.consent.cookie'),
                'categories' => config('ghekatex.consent.categories'),
                'policy_version' => config('ghekatex.consent.policy_version'),
                'lifetime_days' => config('ghekatex.consent.lifetime_days'),
            ],
            'analytics' => $isAdmin ? null : fn () => [
                'ga4' => $this->settings->get('analytics_ga4_id'),
                'metrika' => $this->settings->get('analytics_metrika_id'),
                'gtm' => $this->settings->get('analytics_gtm_id'),
            ],
            'ziggy' => fn () => array_merge((new Ziggy)->toArray(), [
                'location' => $request->url(),
            ]),
        ]);
    }
}

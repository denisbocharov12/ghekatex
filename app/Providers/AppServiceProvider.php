<?php

namespace App\Providers;

use App\Enums\RoleName;
use App\Models\NavigationItem;
use App\Models\Setting;
use App\Services\NavigationService;
use App\Services\SettingService;
use App\Services\TranslationCatalogService;
use App\Support\Translation\OverridableTranslator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SettingService::class);
        $this->app->singleton(NavigationService::class);
        $this->app->singleton(TranslationCatalogService::class);

        // Правки текстов из админки накладываются поверх файлов lang/
        $this->app->extend('translator', fn ($translator, $app) => new OverridableTranslator(
            $translator,
            fn () => $app->make(TranslationCatalogService::class),
        ));
    }

    public function boot(): void
    {
        // Супер-администратор проходит любую проверку прав
        Gate::before(fn ($user, string $ability) => $user->hasRole(RoleName::SuperAdmin->value) ? true : null);

        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Кеш настроек и меню живёт бессрочно, поэтому сбрасываем его на записи
        Setting::saved(fn () => app(SettingService::class)->flush());
        Setting::deleted(fn () => app(SettingService::class)->flush());
        NavigationItem::saved(fn () => app(NavigationService::class)->flush());
        NavigationItem::deleted(fn () => app(NavigationService::class)->flush());
    }
}

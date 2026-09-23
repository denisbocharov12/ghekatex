<?php

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AdvantagesController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CertificatesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FabricsController;
use App\Http\Controllers\Admin\FacilitiesController;
use App\Http\Controllers\Admin\FaqsController;
use App\Http\Controllers\Admin\HeroSlidesController;
use App\Http\Controllers\Admin\MediaAlbumsController;
use App\Http\Controllers\Admin\MediaItemsController;
use App\Http\Controllers\Admin\MilestonesController;
use App\Http\Controllers\Admin\NavigationController;
use App\Http\Controllers\Admin\OfficesController;
use App\Http\Controllers\Admin\PagesController;
use App\Http\Controllers\Admin\PartnersController;
use App\Http\Controllers\Admin\PostCategoriesController;
use App\Http\Controllers\Admin\PostsController;
use App\Http\Controllers\Admin\ProductCategoriesController;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\RedirectsController;
use App\Http\Controllers\Admin\RequestsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\ServicesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\TranslationsController;
use App\Http\Controllers\Admin\TreatmentsController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Панель управления
|--------------------------------------------------------------------------
|
| Все разделы закрыты правами вида `<область>.<действие>`. Роль super-admin
| проходит проверки через Gate::before в AppServiceProvider.
|
*/

Route::middleware('guest')->group(function (): void {
    Route::get('login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1')
        ->name('login.attempt');
});

Route::middleware('auth')->group(function (): void {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/', DashboardController::class)
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');

    /*
    | Контентные разделы с одинаковым набором действий.
    | Ключ массива — сегмент URL, значения — контроллер и область прав.
    */
    $resources = [
        'pages' => [PagesController::class, 'pages'],
        'hero-slides' => [HeroSlidesController::class, 'hero-slides'],
        'advantages' => [AdvantagesController::class, 'advantages'],
        'milestones' => [MilestonesController::class, 'milestones'],
        'facilities' => [FacilitiesController::class, 'facilities'],
        'certificates' => [CertificatesController::class, 'certificates'],
        'partners' => [PartnersController::class, 'partners'],
        'product-categories' => [ProductCategoriesController::class, 'product-categories'],
        'products' => [ProductsController::class, 'products'],
        'fabrics' => [FabricsController::class, 'fabrics'],
        'treatments' => [TreatmentsController::class, 'treatments'],
        'services' => [ServicesController::class, 'services'],
        'faqs' => [FaqsController::class, 'faqs'],
        'post-categories' => [PostCategoriesController::class, 'post-categories'],
        'posts' => [PostsController::class, 'posts'],
        'media-albums' => [MediaAlbumsController::class, 'media'],
        'media-items' => [MediaItemsController::class, 'media'],
        'offices' => [OfficesController::class, 'offices'],
        'navigation' => [NavigationController::class, 'navigation'],
    ];

    foreach ($resources as $segment => [$controller, $area]) {
        Route::prefix($segment)->name($segment.'.')->group(function () use ($controller, $area): void {
            Route::get('/', [$controller, 'index'])->middleware("permission:{$area}.view")->name('index');
            Route::get('create', [$controller, 'create'])->middleware("permission:{$area}.create")->name('create');
            Route::post('/', [$controller, 'store'])->middleware("permission:{$area}.create")->name('store');
            Route::post('reorder', [$controller, 'reorder'])->middleware("permission:{$area}.update")->name('reorder');
            Route::get('{item}/edit', [$controller, 'edit'])->middleware("permission:{$area}.view")->name('edit');
            Route::post('{item}', [$controller, 'update'])->middleware("permission:{$area}.update")->name('update');
            Route::patch('{item}/toggle', [$controller, 'toggle'])->middleware("permission:{$area}.update")->name('toggle');
            Route::delete('{item}', [$controller, 'destroy'])->middleware("permission:{$area}.delete")->name('destroy');
            Route::post('{item}/media/reorder', [$controller, 'reorderMedia'])->middleware("permission:{$area}.update")->name('media.reorder');
            Route::delete('{item}/media/{media}', [$controller, 'destroyMedia'])->middleware("permission:{$area}.update")->name('media.destroy');
        });
    }

    Route::prefix('requests')->name('requests.')->group(function (): void {
        Route::get('/', [RequestsController::class, 'index'])->middleware('permission:requests.view')->name('index');
        Route::get('{item}', [RequestsController::class, 'show'])->middleware('permission:requests.view')->name('show');
        Route::patch('{item}', [RequestsController::class, 'update'])->middleware('permission:requests.update')->name('update');
        Route::delete('{item}', [RequestsController::class, 'destroy'])->middleware('permission:requests.delete')->name('destroy');
    });

    Route::prefix('settings')->name('settings.')->group(function (): void {
        Route::get('/', [SettingsController::class, 'index'])->middleware('permission:settings.view')->name('index');
        Route::post('/', [SettingsController::class, 'update'])->middleware('permission:settings.update')->name('update');
    });

    Route::prefix('translations')->name('translations.')->group(function (): void {
        Route::get('/', [TranslationsController::class, 'index'])->middleware('permission:translations.view')->name('index');
        Route::post('/', [TranslationsController::class, 'update'])->middleware('permission:translations.update')->name('update');
    });

    Route::prefix('redirects')->name('redirects.')->group(function (): void {
        Route::get('/', [RedirectsController::class, 'index'])->middleware('permission:redirects.view')->name('index');
        Route::post('/', [RedirectsController::class, 'store'])->middleware('permission:redirects.create')->name('store');
        Route::patch('{item}', [RedirectsController::class, 'update'])->middleware('permission:redirects.update')->name('update');
        Route::delete('{item}', [RedirectsController::class, 'destroy'])->middleware('permission:redirects.delete')->name('destroy');
    });

    Route::prefix('users')->name('users.')->group(function (): void {
        Route::get('/', [UsersController::class, 'index'])->middleware('permission:users.view')->name('index');
        Route::get('create', [UsersController::class, 'create'])->middleware('permission:users.create')->name('create');
        Route::post('/', [UsersController::class, 'store'])->middleware('permission:users.create')->name('store');
        Route::get('{item}/edit', [UsersController::class, 'edit'])->middleware('permission:users.view')->name('edit');
        Route::post('{item}', [UsersController::class, 'update'])->middleware('permission:users.update')->name('update');
        Route::delete('{item}', [UsersController::class, 'destroy'])->middleware('permission:users.delete')->name('destroy');
    });

    Route::prefix('roles')->name('roles.')->group(function (): void {
        Route::get('/', [RolesController::class, 'index'])->middleware('permission:roles.view')->name('index');
        Route::post('/', [RolesController::class, 'store'])->middleware('permission:roles.create')->name('store');
        Route::patch('{role}', [RolesController::class, 'update'])->middleware('permission:roles.update')->name('update');
        Route::delete('{role}', [RolesController::class, 'destroy'])->middleware('permission:roles.delete')->name('destroy');
    });

    Route::get('activity-log', ActivityLogController::class)
        ->middleware('permission:activity-log.view')
        ->name('activity-log.index');
});

<?php

use App\Http\Controllers\Site\AboutController;
use App\Http\Controllers\Site\CatalogController;
use App\Http\Controllers\Site\ConsentController;
use App\Http\Controllers\Site\ContactController;
use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\MediaController;
use App\Http\Controllers\Site\NewsController;
use App\Http\Controllers\Site\PageController;
use App\Http\Controllers\Site\ServiceController;
use App\Http\Controllers\Site\SitemapController;
use App\Http\Controllers\Site\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Витрина
|--------------------------------------------------------------------------
|
| Язык — первый сегмент пути. Корень отдаёт язык, определённый посредником
| SetLocale, без лишнего редиректа.
|
*/

Route::get('/', HomeController::class)->name('root');

Route::get('sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::post('consent', ConsentController::class)
    ->middleware('throttle:20,1')
    ->name('consent.store');

Route::prefix('{locale}')
    ->whereIn('locale', config('ghekatex.locales.available'))
    ->group(function (): void {
        Route::get('/', HomeController::class)->name('home');

        Route::get('about', AboutController::class)->name('about');

        Route::get('catalog', [CatalogController::class, 'index'])->name('catalog.index');
        Route::get('catalog/category/{category:slug}', [CatalogController::class, 'category'])->name('catalog.category');
        Route::get('catalog/{product:slug}', [CatalogController::class, 'show'])->name('catalog.show');

        Route::get('services', [ServiceController::class, 'index'])->name('services.index');
        Route::get('services/{service:slug}', [ServiceController::class, 'show'])->name('services.show');

        Route::get('news', [NewsController::class, 'index'])->name('news.index');
        Route::get('news/{post:slug}', [NewsController::class, 'show'])->name('news.show');

        Route::get('media', [MediaController::class, 'index'])->name('media.index');
        Route::get('media/{album}', [MediaController::class, 'show'])->name('media.show');

        Route::get('contacts', [ContactController::class, 'index'])->name('contacts.index');
        Route::post('contacts', [ContactController::class, 'store'])
            ->middleware('throttle:'.config('ghekatex.forms.throttle'))
            ->name('contacts.store');

        Route::post('subscribe', [SubscriptionController::class, 'store'])
            ->middleware('throttle:'.config('ghekatex.forms.throttle'))
            ->name('subscribe.store');
        Route::get('unsubscribe/{token}', [SubscriptionController::class, 'destroy'])->name('unsubscribe');

        // Контентные страницы: «о компании», политика конфиденциальности, cookie, условия
        Route::get('{page:slug}', [PageController::class, 'show'])->name('pages.show');
    });

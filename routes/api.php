<?php

use App\Http\Controllers\Api\V1\CatalogController;
use App\Http\Controllers\Api\V1\NewsController;
use App\Http\Controllers\Api\V1\ServiceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Публичный API v1
|--------------------------------------------------------------------------
|
| Только чтение: витрина работает через Inertia, API нужен для партнёрских
| интеграций и выгрузки каталога. Язык ответа задаётся заголовком
| Accept-Language или параметром ?locale=.
|
*/

Route::prefix('v1')->name('api.v1.')->middleware('throttle:60,1')->group(function (): void {
    Route::get('categories', [CatalogController::class, 'categories'])->name('categories');
    Route::get('products', [CatalogController::class, 'products'])->name('products');
    Route::get('products/{slug}', [CatalogController::class, 'product'])->name('products.show');

    Route::get('services', [ServiceController::class, 'index'])->name('services');
    Route::get('services/{slug}', [ServiceController::class, 'show'])->name('services.show');

    Route::get('posts', [NewsController::class, 'index'])->name('posts');
    Route::get('posts/{slug}', [NewsController::class, 'show'])->name('posts.show');
});

<?php

declare(strict_types=1);

use App\Http\Middleware\FileUrlMiddleware;
use Illuminate\Support\Facades\Route;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here you can register the tenant routes for your application.
| These routes are loaded by the TenantRouteServiceProvider.
|
| Feel free to customize them however you want. Good luck!
|
*/

Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
    FileUrlMiddleware::class,
])->group(function () {

    Route::livewire('/invoice/{code}', 'pages::tenant.invoice.show')->name('invoice.show');

    Route::view('/', 'pages.tenant.index')->name('index');

    Route::middleware('auth')->group(function () {

        Route::livewire('cashier', 'pages::tenant.pos.index')->name('cashier');

        Route::prefix('dashboard')->group(function () {
            Route::livewire('/', 'pages::tenant.dashboard')->name('dashboard');
            Route::view('order', 'pages.tenant.order.index')->name('order');
            Route::view('product', 'pages.tenant.product.product')->name('product');
            Route::livewire('product/create', 'pages::tenant.product.form')->name('product.create');
            Route::livewire('product/{product}/edit', 'pages::tenant.product.form')->name('product.edit');
            Route::livewire('store-setting', 'pages::tenant.setting.store-setting')->name('store-setting');
            Route::view('user', 'pages.tenant.user.index')->name('user');
            Route::livewire('profile', 'pages::tenant.profile.user-profile')->name('profile');
        });
    });

    Route::prefix('api')->middleware(['api'])->group(function () {
        Route::get('/restaurant', \App\Http\Controllers\Api\RestaurantApiController::class);
        Route::get('/categories', \App\Http\Controllers\Api\CategoryApiController::class);
        Route::get('/products', [\App\Http\Controllers\Api\ProductApiController::class, 'index']);
        Route::get('/products/{productId}', [\App\Http\Controllers\Api\ProductApiController::class, 'show']);
        Route::post('/orders', [\App\Http\Controllers\Api\OrderApiController::class, 'store']);
    });

    require __DIR__ . '/auth.php';
});

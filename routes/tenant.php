<?php

declare(strict_types=1);

use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\RestaurantApiController;
use App\Http\Controllers\MenuController;
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

    Route::get('/menu/{productId}', [MenuController::class, 'showProductPreview'])->name('product.preview');
    Route::get('/menu/{productId}/story', [MenuController::class, 'shareAsStory'])->name('product.story');
    Route::get('/menu/{productId}/story/image', [MenuController::class, 'generateStoryImage'])->name('product.story.image');

    Route::middleware('auth')->group(function () {

        // Routes accessible by manager AND cashier
        Route::middleware('role:manager,cashier')->group(function () {
            Route::livewire('cashier', 'pages::tenant.pos.index')->name('cashier');
            Route::view('order', 'pages.tenant.order.index')->name('order');
            Route::livewire('profile', 'pages::tenant.profile.user-profile')->name('profile');
        });

        // Routes accessible ONLY by manager
        Route::middleware('role:manager')->group(function () {
            Route::livewire('dashboard', 'pages::tenant.dashboard')->name('dashboard');
            Route::view('product', 'pages.tenant.product.product')->name('product');
            Route::livewire('product/create', 'pages::tenant.product.form')->name('product.create');
            Route::livewire('product/{product}/edit', 'pages::tenant.product.form')->name('product.edit');
            Route::livewire('store-setting', 'pages::tenant.setting.store-setting')->name('store-setting');
            Route::livewire('product-slot/buy', 'pages::tenant.setting.buy-product-slot')->name('product-slot.buy');
            Route::view('user', 'pages.tenant.user.index')->name('user');
        });
    });

    Route::prefix('api')->middleware(['api'])->group(function () {
        Route::get('/restaurant', RestaurantApiController::class);
        Route::get('/categories', CategoryApiController::class);
        Route::get('/products', [ProductApiController::class, 'index']);
        Route::get('/products/{productId}', [ProductApiController::class, 'show']);
        Route::post('/orders', [OrderApiController::class, 'store']);
    });

    require __DIR__ . '/auth.php';
});

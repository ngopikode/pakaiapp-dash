<?php

declare(strict_types=1);

use App\Tenant\Controllers\Api\DuitkuApiController;
use App\Tenant\Controllers\Api\OrderApiController;
use App\Tenant\Controllers\Api\OrderHistoryApiController;
use App\Tenant\Controllers\Api\RestaurantApiController;
use App\Tenant\Controllers\Web\HomeController;
use App\Tenant\Controllers\Web\MenuController;
use App\Tenant\Controllers\Web\TenantManifestController;
use App\Shared\Middleware\FileUrlMiddleware;
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
    Route::livewire('/order/{code}', 'pages::tenant.order.show')->name('order.show');

    Route::get('/manifest.json', TenantManifestController::class);

    Route::get('/', HomeController::class)->name('index');

    Route::controller(MenuController::class)->prefix('menu')->name('product.')->group(function () {
        Route::get('/{product}', 'show')->name('show');
        Route::get('/{product}/story', 'shareAsStory')->name('story');
    });

    Route::middleware('auth')->group(function () {

        // Routes accessible by manager AND cashier
        Route::middleware('role:manager,cashier')->group(function () {
            Route::livewire('cashier', 'pages::tenant.pos.index')->name('cashier');
            Route::view('order', 'pages.tenant.order.index')->name('order');
            Route::livewire('profile', 'pages::tenant.profile.user-profile')->name('profile');
            Route::livewire('menu', 'pages::tenant.mobile-menu')->name('menu');
        });

        // Routes accessible by manager AND kitchen
        Route::middleware('role:manager,kitchen')->group(function () {
            Route::livewire('kitchen', 'pages::tenant.kitchen')->name('kitchen');
        });

        // Routes accessible ONLY by manager
        Route::middleware('role:manager')->group(function () {
            Route::livewire('dashboard', 'pages::tenant.dashboard')->name('dashboard');
            Route::livewire('ai-engine', 'pages::tenant.ai-engine-manager')->name('ai-engine');
            Route::livewire('wallet', 'pages::tenant.payment.wallet')->name('wallet');
            Route::view('product', 'pages.tenant.product.product')->name('product');
            Route::livewire('product/create', 'pages::tenant.product.form')->name('product.create');
            Route::livewire('product/{product}/edit', 'pages::tenant.product.form')->name('product.edit');
            Route::livewire('raw-material', 'pages::tenant.resto.raw-material')->name('raw-material');
            Route::livewire('store-setting', 'pages::tenant.setting.store-setting')->name('store-setting');
            Route::livewire('product-slot/buy', 'pages::tenant.setting.buy-product-slot')->name('product-slot.buy');
            Route::view('user', 'pages.tenant.user.index')->name('user');
        });
    });

    Route::prefix('api')->middleware(['api'])->group(function () {
        Route::get('/restaurant', RestaurantApiController::class)->name('api.restaurant');
        
        Route::prefix('orders')->name('api.orders.')->group(function () {
            Route::post('/', [OrderApiController::class, 'store'])->middleware('throttle:orders')->name('store');
            Route::post('/history', [OrderHistoryApiController::class, 'index'])->middleware('throttle:30,1')->name('history');
        });

        // ─── Duitku — callback/return/status sudah pindah ke central domain ────
        // Payment methods: tetap di tenant karena butuh context tenant untuk amount
        Route::prefix('duitku')->name('duitku.')->group(function () {
            Route::get('/payment-methods', [DuitkuApiController::class, 'getPaymentMethods'])->name('payment-methods');
        });
    });

    require __DIR__ . '/auth.php';
});

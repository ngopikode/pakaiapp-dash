<?php

declare(strict_types=1);

use App\Shared\Middleware\FileUrlMiddleware;
use App\Tenant\Controllers\Web\CashierController;
use App\Tenant\Controllers\Web\HomeController;
use App\Tenant\Controllers\Web\MenuController;
use App\Tenant\Controllers\Web\TenantManifestController;
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
    /*
    |──────────────────────────────────────────────────────────────────────────────
    | Public Routes
    |──────────────────────────────────────────────────────────────────────────────
    */
    Route::get('/', HomeController::class)->name('index');
    Route::get('/manifest.json', TenantManifestController::class);

    Route::controller(MenuController::class)->prefix('menu')->name('menu.')->group(function () {
        Route::get('/{product}', 'show')->name('show');
        Route::get('/{product}/story', 'shareAsStory')->name('story');
    });
    Route::livewire('/invoice/{code}', 'pages::tenant.invoice.show')->name('invoice.show');
    Route::livewire('/receipt/{code}', 'pages::tenant.receipt.show')->name('receipt.show');
    Route::livewire('/order/{code}', 'pages::tenant.order.show')->name('order.show');
    /*
    |──────────────────────────────────────────────────────────────────────────────
    | Authenticated Routes
    |──────────────────────────────────────────────────────────────────────────────
    */
    Route::middleware('auth')->group(function () {
        // Routes accessible by manager AND cashier
        Route::middleware('role:manager,cashier')->group(function () {
            Route::get('cashier', CashierController::class)->name('cashier');
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
            Route::livewire('cashbook', 'pages::tenant.finance.cashbook')->name('cashbook');
            Route::livewire('buku-kas', 'pages::tenant.finance.buku-kas')->name('buku-kas');
            Route::view('products', 'pages.tenant.product.product')->name('products');
            Route::livewire('raw-material', 'pages::tenant.resto.raw-material')->name('raw-material');
            Route::livewire('store-setting', 'pages::tenant.setting.store-setting')->name('store-setting');
            Route::livewire('product-slot/buy', 'pages::tenant.setting.buy-product-slot')->name('product-slot.buy');
            Route::view('user', 'pages.tenant.user.index')->name('user');
        });
    });
    require __DIR__ . '/tenant/api.php';
    require __DIR__ . '/auth.php';
});

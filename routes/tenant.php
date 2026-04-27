<?php

declare(strict_types=1);

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
])->group(function () {

    Route::livewire('/invoice/{code}', 'pages::tenant.invoice.show')->name('invoice.show');

    Route::middleware('auth')->group(function () {

        Route::livewire('cashier', 'pages::tenant.pos.index')->name('cashier');

        Route::prefix('dashboard')->group(function () {
            Route::livewire('/', 'pages::tenant.dashboard')->name('dashboard');
            Route::livewire('order', 'pages::tenant.order.index')->name('order');
            Route::view('product', 'pages.tenant.product.product')->name('product');
            Route::livewire('product/create', 'pages::tenant.product.create')->name('product.create');
            Route::livewire('profile', 'pages::tenant.profile.user-profile')->name('profile');
        });
    });

    require __DIR__ . '/auth.php';
});

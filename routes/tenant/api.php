<?php

use App\Tenant\Controllers\Api\DuitkuApiController;
use App\Tenant\Controllers\Api\OrderApiController;
use App\Tenant\Controllers\Api\OrderHistoryApiController;
use App\Tenant\Controllers\Api\RestaurantApiController;

/**
 * Tenant API Routes
 * * Di sini Anda dapat mendaftarkan semua API routes untuk tenant.
 * Route ini dimuat oleh RouteServiceProvider dan otomatis menggunakan
 * middleware 'api'.
 */
Route::prefix('api')->middleware(['api'])->name('api.')->group(function () {

    /**
     * Restaurant Information
     */
    Route::get('/restaurant', RestaurantApiController::class)->name('restaurant');

    /**
     * Orders Management
     */
    Route::prefix('orders')->name('orders.')->group(function () {
        Route::post('/', [OrderApiController::class, 'store'])->middleware(['throttle:orders', 'store.open'])->name('store');
        Route::post('/history', [OrderHistoryApiController::class, 'index'])->middleware('throttle:30,1')->name('history');
    });

    /**
     * Payment Gateway (Duitku)
     *
     * Catatan: Callback, return, dan status url sudah dipindahkan ke Central Domain.
     * Endpoint ini tetap di Tenant Domain karena membutuhkan context tenant untuk kalkulasi amount.
     */
    Route::prefix('duitku')->name('duitku.')->group(function () {
        Route::get('/payment-methods', [DuitkuApiController::class, 'getPaymentMethods'])->name('payment-methods');
    });

});

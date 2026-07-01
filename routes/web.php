<?php

use App\Central\Controllers\ArticleController;
use App\Central\Controllers\AuthController;
use App\Central\Controllers\DuitkuController;
use App\Central\Controllers\MidtransController;
use App\Central\Http\Middleware\DuitkuEnabled;
use App\Shared\Middleware\IpWhitelist;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::view('/', 'welcome')->name('home');
        Route::view('/kasir-cafe', 'pages.landing-cafe')->name('landing.cafe');
        Route::view('/kasir-toko-kelontong', 'pages.landing-retail')->name('landing.retail');

        Route::get('/blog', [ArticleController::class, 'index'])->name('blog.index');
        Route::get('/blog/{slug}', [ArticleController::class, 'show'])->name('blog.show');

        Route::livewire('central-admin', 'pages::central.central-admin')->name('central-admin');

        // Central Pages (Register, Login, Status Onboarding)
        Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
        Route::get('/login', [AuthController::class, 'showLogin'])->name('central.login');

        Route::get('/register/status/{invoice_code}', [AuthController::class, 'registerStatus'])->name('register.status');
        Route::get('/api/register/status/{invoice_code}', [AuthController::class, 'apiRegisterStatus']);

        Route::post('/api/central-login', [AuthController::class, 'centralLogin']);

        // Email Verification OTP Endpoints
        Route::post('/api/request-otp', [AuthController::class, 'requestOtp']);
        Route::post('/api/verify-otp', [AuthController::class, 'verifyOtp']);

        // Self-Serve Tenant Registration Handler
        Route::post('/api/register-tenant', [AuthController::class, 'registerTenant']);

        // Fetch Duitku Payment Methods for Onboarding
        Route::get('/api/duitku/payment-methods', [DuitkuController::class, 'getPaymentMethods'])
            ->middleware(DuitkuEnabled::class);

        // ─── Duitku Payment Gateway — Central Callbacks ───────────────────────
        Route::post('/duitku/callback', [DuitkuController::class, 'callback'])
            ->name('duitku.callback')
            ->middleware([IpWhitelist::class, DuitkuEnabled::class]);
        Route::get('/duitku/return', [DuitkuController::class, 'return'])
            ->name('duitku.return')
            ->middleware(DuitkuEnabled::class);
        Route::get('/duitku/status/{invoiceCode}', [DuitkuController::class, 'status'])
            ->name('duitku.status')
            ->where('invoiceCode', '[A-Za-z0-9\-~_]+')
            ->middleware(DuitkuEnabled::class);

        // ─── Midtrans Payment Gateway — Central Callbacks ───────────────────────
        Route::post('/midtrans/notification', [MidtransController::class, 'notification'])
            ->name('midtrans.notification')
            ->middleware(IpWhitelist::class);
    });
}

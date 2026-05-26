<?php

use App\Http\Controllers\CentralAuthController;
use App\Http\Controllers\CentralDuitkuController;
use App\Http\Controllers\CentralMidtransController;
use App\Http\Middleware\DuitkuIpWhitelist;
use App\Http\Middleware\MidtransIpWhitelist;
use App\Services\DuitkuService;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::view('/', 'welcome')->name('home');
        Route::livewire('central-admin', 'pages::central.central-admin')->name('central-admin');

        // Central Pages (Register, Login, Status Onboarding)
        Route::get('/register', [CentralAuthController::class, 'showRegister'])->name('register');
        Route::get('/login', [CentralAuthController::class, 'showLogin'])->name('login');

        Route::get('/register/status/{invoice_code}', [CentralAuthController::class, 'registerStatus'])->name('register.status');
        Route::get('/api/register/status/{invoice_code}', [CentralAuthController::class, 'apiRegisterStatus']);

        Route::post('/api/central-login', [CentralAuthController::class, 'centralLogin']);

        // Email Verification OTP Endpoints
        Route::post('/api/request-otp', [CentralAuthController::class, 'requestOtp']);
        Route::post('/api/verify-otp', [CentralAuthController::class, 'verifyOtp']);

        // Self-Serve Tenant Registration Handler
        Route::post('/api/register-tenant', [CentralAuthController::class, 'registerTenant']);

        // Fetch Duitku Payment Methods for Onboarding
        Route::get('/api/duitku/payment-methods', function (\Illuminate\Http\Request $request) {
            if (!config('duitku.enabled')) {
                return response()->json(['success' => false, 'message' => 'Duitku payment gateway is disabled.'], 403);
            }
            $request->validate(['amount' => 'required|numeric|min:1']);
            try {
                $service = new DuitkuService();
                $methods = $service->getPaymentMethods((int)$request->amount);
                return response()->json(['success' => true, 'data' => $methods]);
            } catch (Throwable $e) {
                \Illuminate\Support\Facades\Log::error('[Duitku Central] getPaymentMethods error', ['error' => $e->getMessage()]);
                return response()->json(['success' => false, 'message' => 'Gagal mengambil metode pembayaran.'], 500);
            }
        });

        // ─── Duitku Payment Gateway — Central Callbacks ───────────────────────
        Route::post('/duitku/callback', [CentralDuitkuController::class, 'callback'])
            ->name('duitku.callback')
            ->middleware(DuitkuIpWhitelist::class);
        Route::get('/duitku/return', [CentralDuitkuController::class, 'return'])
            ->name('duitku.return');
        Route::get('/duitku/status/{invoiceCode}', [CentralDuitkuController::class, 'status'])
            ->name('duitku.status')
            ->where('invoiceCode', '[A-Za-z0-9\-~_]+');

        // ─── Midtrans Payment Gateway — Central Callbacks ───────────────────────
        Route::post('/midtrans/notification', [CentralMidtransController::class, 'notification'])
            ->name('midtrans.notification')
            ->middleware(MidtransIpWhitelist::class);
    });
}

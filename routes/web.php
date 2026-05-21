<?php

use App\Http\Controllers\CentralDuitkuController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::view('/', 'welcome')->name('home');
        Route::livewire('central-admin', 'pages::central.central-admin')->name('central-admin');

        // ─── Duitku Payment Gateway — Central Callbacks ───────────────────────
        // Semua callback/return Duitku masuk ke sini via api.pakaiapp.online.
        // Tidak ada auth/CSRF karena dipanggil server Duitku, bukan browser.
        // Validasi keamanan dilakukan via signature di DuitkuService::handleCallback().
        //
        // Callback: POST dari server Duitku setiap ada update status transaksi
        Route::post('/duitku/callback', [CentralDuitkuController::class, 'callback'])
            ->name('duitku.callback');

        // Return: GET — customer diredirect kesini setelah bayar
        Route::get('/duitku/return', [CentralDuitkuController::class, 'return'])
            ->name('duitku.return');

        // Status: GET — polling status dari frontend/kasir
        // Format invoiceCode: "{tenantId}~{invoiceCode}"
        Route::get('/duitku/status/{invoiceCode}', [CentralDuitkuController::class, 'status'])
            ->name('duitku.status')
            ->where('invoiceCode', '[A-Za-z0-9\-~_]+');
    });
}

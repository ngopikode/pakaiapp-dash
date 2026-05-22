<?php

use App\Http\Controllers\CentralDuitkuController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::view('/', 'welcome')->name('home');
        Route::livewire('central-admin', 'pages::central.central-admin')->name('central-admin');

        // Lead submission handler from the chatbot
        Route::post('/api/send-lead', function (\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'jenisBisnis' => 'required|string|max:100',
                'jumlahCabang' => 'required|string|max:100',
                'namaToko' => 'required|string|max:100',
                'namaOwner' => 'required|string|max:100',
                'noWa' => 'required|string|max:50',
            ]);

            // Log the lead for the administrators in laravel.log
            \Illuminate\Support\Facades\Log::info('New Lead Received from landing page:', $validated);

            // Optional: Send to Telegram Bot if credentials are provided in .env
            $botToken = '7725874331:AAHNnwXrnkymBJEfD0PaPZFNsemwcB77vsI';
            $chatId = '5554516703';

            if ($botToken && $chatId) {
                try {
                    $waClean = preg_replace('/[^0-9]/', '', $validated['noWa']);
                    // If no wa begins with '0', replace with '62'
                    if (str_starts_with($waClean, '0')) {
                        $waClean = '62' . substr($waClean, 1);
                    }
                    
                    $message = "🚀 *New Lead Registered on Pakaiapp!*\n\n"
                        . "👤 *Owner:* " . $validated['namaOwner'] . "\n"
                        . "🏪 *Toko:* " . $validated['namaToko'] . "\n"
                        . "🍔 *Bisnis:* " . $validated['jenisBisnis'] . "\n"
                        . "🏢 *Cabang:* " . $validated['jumlahCabang'] . "\n"
                        . "📞 *WhatsApp:* [https://wa.me/{$waClean}](https://wa.me/{$waClean})\n";

                    \Illuminate\Support\Facades\Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                        'chat_id' => $chatId,
                        'text' => $message,
                        'parse_mode' => 'Markdown',
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send lead to Telegram: ' . $e->getMessage());
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Lead successfully registered.'
            ]);
        })->name('api.send-lead');

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

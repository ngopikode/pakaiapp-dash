<?php

use App\Http\Controllers\CentralDuitkuController;
use App\Http\Controllers\CentralMidtransController;
use Illuminate\Support\Facades\Route;

foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        Route::view('/', 'welcome')->name('home');
        Route::livewire('central-admin', 'pages::central.central-admin')->name('central-admin');

        // Self-Serve Tenant Registration Handler
        Route::post('/api/register-tenant', function (\Illuminate\Http\Request $request) {
            $validated = $request->validate([
                'jenisBisnis' => 'required|string|max:100',
                'namaToko' => 'required|string|max:100',
                'namaOwner' => 'required|string|max:100',
                'noWa' => 'required|string|max:50',
                'email' => 'required|email|max:150',
                'password' => 'required|string|min:6',
                'paket' => 'required|in:free,santai,premium',
                'payment_method' => 'required|string|max:50',
            ]);

            // Sanitize WhatsApp
            $waClean = preg_replace('/[^0-9]/', '', $validated['noWa']);
            if (str_starts_with($waClean, '0')) {
                $waClean = '62' . substr($waClean, 1);
            }

            // Create tenant slug/subdomain
            $slug = \Illuminate\Support\Str::slug($validated['namaToko']);
            if (\App\Models\Tenant::where('id', $slug)->exists() || \App\Models\TenantRegistration::where('tenant_id', $slug)->where('status', 'paid')->exists()) {
                return response()->json(['status' => 'error', 'message' => 'Nama toko (subdomain) ini sudah terpakai. Silakan gunakan nama lain.']);
            }

            $amount = 0;
            if ($validated['paket'] === 'santai') {
                $amount = 50000;
            } elseif ($validated['paket'] === 'premium') {
                $amount = 150000;
            }

            // Save Registration to Database
            $registration = \App\Models\TenantRegistration::create([
                'owner_name' => $validated['namaOwner'],
                'email' => $validated['email'],
                'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
                'store_name' => $validated['namaToko'],
                'store_type' => $validated['jenisBisnis'] === 'F&B (Resto/Cafe)' ? 'resto' : 'retail',
                'tenant_id' => $slug,
                'whatsapp' => $waClean,
                'plan' => $validated['paket'],
                'amount' => $amount,
                'status' => 'pending',
                'payment_method' => $validated['payment_method'],
            ]);

            // Jika gratis, langsung proses pembuatan tenant dan kembalikan status success tanpa Snap Token
            if ($amount == 0) {
                // Proses Create Tenant Langsung
                try {
                    $domainUrl = $slug . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');
                    \Illuminate\Support\Facades\Artisan::call('tenant:create', [
                        'name' => $validated['namaToko'],
                        '--type' => $registration->store_type,
                        '--domain' => $domainUrl,
                        '--plan' => 'free',
                    ]);

                    // Update User Email & Password di dalam Tenant
                    $tenant = \App\Models\Tenant::find($slug);
                    $tenant?->run(function () use ($registration) {
                        \App\Models\User::firstOrCreate(
                            ['email' => $registration->email],
                            [
                                'name' => $registration->owner_name,
                                'password' => $registration->password,
                                'role' => 'manager'
                            ]
                        );
                    });

                    $registration->update(['status' => 'created']);
                    
                    // Send Welcome Email
                    $emailTitle = "Toko " . $registration->store_name . " Siap Digunakan!";
                    $emailBody = "Halo {$registration->owner_name},\n\nSelamat bergabung di Pakaiapp! Sistem kasir toko Anda ({$registration->store_name}) telah selesai disiapkan.\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://{$domainUrl}/login\nEmail: {$registration->email}\n\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n\nSalam sukses,\nTim Pakaiapp";
                    
                    \Illuminate\Support\Facades\Mail::to($registration->email)->send(
                        new \App\Mail\SystemEmail($emailTitle, $emailBody, 'Buka Dashboard', "https://{$domainUrl}/login")
                    );

                    return response()->json([
                        'status' => 'success',
                        'message' => 'Toko berhasil dibuat! Anda akan dialihkan ke dashboard.',
                        'redirect_url' => 'https://' . $domainUrl . '/login'
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Free registration failed: " . $e->getMessage());
                    
                    // Send Failure Email
                    $emailTitle = "Pendaftaran Toko Gagal";
                    $emailBody = "Halo {$registration->owner_name},\n\nMohon maaf, terjadi kesalahan sistem saat menyiapkan toko Anda ({$registration->store_name}). Tim kami sedang menangani masalah ini.\n\nSilakan coba beberapa saat lagi atau hubungi support kami jika masalah berlanjut.\n\nSalam,\nTim Pakaiapp";
                    
                    try {
                        \Illuminate\Support\Facades\Mail::to($registration->email)->send(
                            new \App\Mail\SystemEmail($emailTitle, $emailBody, 'Hubungi Support', "https://wa.me/6285172441544")
                        );
                    } catch (\Exception $mailEx) {
                        \Illuminate\Support\Facades\Log::error("Failed to send failure email: " . $mailEx->getMessage());
                    }

                    return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem saat membuat toko.']);
                }
            }

            // Jika Manual (WA)
            if ($validated['payment_method'] === 'manual') {
                $text = "Halo Admin Pakaiapp, saya ingin mendaftar toko baru dengan rincian:\n"
                      . "Nama Toko: {$registration->store_name}\n"
                      . "Pemilik: {$registration->owner_name}\n"
                      . "Email: {$registration->email}\n"
                      . "Paket: " . ucfirst($registration->plan) . "\n"
                      . "Mohon info rekening untuk pembayaran sebesar Rp " . number_format($amount, 0, ',', '.') . ". Terima kasih.";
                      
                $waUrl = "https://wa.me/6285172441544?text=" . urlencode($text);
                
                return response()->json([
                    'status' => 'manual',
                    'redirect_url' => $waUrl
                ]);
            }

            // Jika berbayar via Midtrans
            if ($validated['payment_method'] === 'midtrans') {
                try {
                    $midtransService = app(\App\Services\MidtransService::class);
                    $snapToken = $midtransService->createRegistrationSnapToken($registration);
                    $registration->update(['snap_token' => $snapToken]);

                    return response()->json([
                        'status' => 'payment_required_midtrans',
                        'snap_token' => $snapToken,
                    ]);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Gagal create snap token registrasi: " . $e->getMessage());
                    return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke layanan pembayaran Midtrans.']);
                }
            }
            
            // Jika berbayar via Duitku
            try {
                $duitkuService = app(\App\Services\DuitkuService::class);
                $duitkuInvoice = $duitkuService->createRegistrationInvoice($registration, $validated['payment_method']);
                
                $registration->update([
                    'duitku_payment_url' => $duitkuInvoice['payment_url'],
                    'duitku_reference' => $duitkuInvoice['reference']
                ]);

                return response()->json([
                    'status' => 'payment_required_duitku',
                    'payment_url' => $duitkuInvoice['payment_url']
                ]);
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Gagal create Duitku invoice registrasi: " . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke layanan pembayaran Duitku.']);
            }
        });

        // ─── Duitku Payment Gateway — Central Callbacks ───────────────────────
        Route::post('/duitku/callback', [CentralDuitkuController::class, 'callback'])
            ->name('duitku.callback')
            ->middleware(\App\Http\Middleware\DuitkuIpWhitelist::class);
        Route::get('/duitku/return', [CentralDuitkuController::class, 'return'])
            ->name('duitku.return');
        Route::get('/duitku/status/{invoiceCode}', [CentralDuitkuController::class, 'status'])
            ->name('duitku.status')
            ->where('invoiceCode', '[A-Za-z0-9\-~_]+');

        // ─── Midtrans Payment Gateway — Central Callbacks ───────────────────────
        Route::post('/midtrans/notification', [CentralMidtransController::class, 'notification'])
            ->name('midtrans.notification')
            ->middleware(\App\Http\Middleware\MidtransIpWhitelist::class);
    });
}

<?php

namespace App\Http\Controllers;

use App\Mail\SystemEmail;
use App\Models\Tenant;
use App\Models\TenantRegistration;
use App\Models\User;
use App\Services\DuitkuService;
use App\Services\MidtransService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Random\RandomException;

class CentralAuthController extends Controller
{
    private const array DISPOSABLE_EMAIL_DOMAINS = [
        'yopmail.com', 'mailinator.com', 'tempmail.com', '10minutemail.com',
        'sharklasers.com', 'guerrillamail.com', 'dispostable.com', 'getairmail.com',
        'maildrop.cc', 'temp-mail.org', 'fakeinbox.com', 'throwawaymail.com',
        'mailnesia.com', 'mailcatch.com', 'yopmail.fr', 'yopmail.net',
        'cool.fr.nf', 'jetable.org', 'boun.cr', 'trbvm.com'
    ];

    public function showRegister()
    {
        return view('register');
    }

    public function showLogin()
    {
        return view('login');
    }

    public function registerStatus($invoiceCode)
    {
        $registration = TenantRegistration::where('invoice_code', $invoiceCode)->firstOrFail();
        return view('register-status', compact('registration'));
    }

    public function apiRegisterStatus($invoiceCode)
    {
        $registration = TenantRegistration::where('invoice_code', $invoiceCode)->first();
        if (!$registration) {
            return response()->json(['status' => 'failed', 'message' => 'Registration not found'], 404);
        }

        $centralDomain = config('tenancy.central_domains')[2] ?? 'pakaiapp.online';
        $domainUrl = 'https://' . $registration->tenant_id . '.' . $centralDomain . '/auth/login';

        return response()->json([
            'status' => $registration->status,
            'redirect_url' => $domainUrl,
            'payment_url' => $registration->duitku_payment_url ?? null,
        ]);
    }

    public function centralLogin(Request $request)
    {
        $request->validate([
            'login_input' => 'required|string|max:255',
        ]);

        $input = trim($request->login_input);

        // 1. Check if input is Email
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $registrations = TenantRegistration::where('email', $input)
                ->where('status', 'created')
                ->get();

            if ($registrations->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Email ini belum terdaftar atau toko belum selesai disiapkan.'
                ]);
            }

            return response()->json([
                'status' => 'success',
                'type' => 'email',
                'stores' => $registrations->map(function ($reg) {
                    $centralDomain = config('tenancy.central_domains')[2] ?? 'pakaiapp.online';
                    $domainUrl = $reg->tenant_id . '.' . $centralDomain;
                    return [
                        'store_name' => $reg->store_name,
                        'tenant_id' => $reg->tenant_id,
                        'url' => 'https://' . $domainUrl . '/auth/login'
                    ];
                })
            ]);
        }

        // 2. Check if input is Subdomain / Shop name
        $slug = Str::slug($input);
        $tenant = Tenant::find($slug);
        if ($tenant) {
            $domain = $tenant->domains->first()?->domain;
            if (!$domain) {
                $centralDomain = config('tenancy.central_domains')[2] ?? 'pakaiapp.online';
                $domain = $slug . '.' . $centralDomain;
            }
            return response()->json([
                'status' => 'success',
                'type' => 'subdomain',
                'redirect_url' => 'https://' . $domain . '/auth/login'
            ]);
        }

        // 3. Check if shop is pending registration
        $regPending = TenantRegistration::where('tenant_id', $slug)->first();
        if ($regPending) {
            return response()->json([
                'status' => 'error',
                'message' => 'Toko Anda sedang disiapkan atau menunggu pembayaran. Silakan cek status pendaftaran.'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Toko atau Email tidak ditemukan. Harap periksa kembali.'
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws RandomException
     */
    public function requestOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;

        // Check if there is already a verified token (prevent requesting again if already verified)
        if (Cache::get('email_verified_' . $email)) {
            return response()->json(['status' => 'error', 'message' => 'Email ini sudah terverifikasi.']);
        }

        // Generate 6 digit OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 5 minutes
        Cache::put('otp_register_' . $email, $otp, now()->addMinutes(5));

        // Send Email
        $emailTitle = "Kode Verifikasi (OTP) Pendaftaran";
        $emailBody = "Halo,\n\nTerima kasih telah mendaftar di Pakaiapp. Berikut adalah kode OTP Anda untuk verifikasi email:\n\n$otp\n\nKode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun.";

        try {
            Mail::to($email)->send(
                new SystemEmail($emailTitle, $emailBody)
            );
            return response()->json(['status' => 'success', 'message' => 'OTP berhasil dikirim ke email Anda.']);
        } catch (Exception $e) {
            Log::error("Failed to send OTP email: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim email OTP. Silakan coba lagi.']);
        }
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|digits:6'
        ]);

        $cachedOtp = Cache::get('otp_register_' . $request->email);

        if (!$cachedOtp || $cachedOtp !== $request->otp) {
            return response()->json(['status' => 'error', 'message' => 'Kode OTP tidak valid atau sudah kedaluwarsa.']);
        }

        // Mark email as verified for 30 minutes
        Cache::put('email_verified_' . $request->email, true, now()->addMinutes(30));
        // Remove OTP from cache
        Cache::forget('otp_register_' . $request->email);

        return response()->json(['status' => 'success', 'message' => 'Email berhasil diverifikasi!']);
    }

    public function registerTenant(Request $request)
    {
        $validated = $request->validate([
            'jenisBisnis' => 'required|string|max:100',
            'namaToko' => 'required|string|max:100',
            'namaOwner' => 'required|string|max:100',
            'noWa' => 'required|string|max:50',
            'email' => 'required|email|max:150',
            'paket' => 'required|in:free,santai,premium',
            'payment_method' => 'required|string|max:50',
        ]);

        // Validate Email Verification
        if (!Cache::get('email_verified_' . $validated['email'])) {
            return response()->json(['status' => 'error', 'message' => 'Email belum diverifikasi. Silakan verifikasi email terlebih dahulu.']);
        }

        // Sanitize WhatsApp
        $waClean = preg_replace('/[^0-9]/', '', $validated['noWa']);
        if (str_starts_with($waClean, '0')) {
            $waClean = '62' . substr($waClean, 1);
        }

        // ─── FREE TRIAL ABUSE PREVENTION ───
        if ($validated['paket'] === 'free') {
            // Layer 1: IP Rate Limiting (max 2 free registrations per hour per IP)
            $ip = $request->ip();
            $rateKey = 'free_registration_limit_' . $ip;
            if (RateLimiter::tooManyAttempts($rateKey, 2)) {
                $seconds = RateLimiter::availableIn($rateKey);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Batas pendaftaran toko gratis terlampaui untuk perangkat Anda. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.'
                ]);
            }

            // Layer 2: Cookie Fingerprint Protection
            if ($request->hasCookie('pakaiapp_free_trial_claimed')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Perangkat/Browser Anda terdeteksi sudah pernah mendaftarkan Toko Gratis. Untuk mendaftarkan toko tambahan, silakan pilih Paket Santai atau Paket Premium.'
                ]);
            }

            // Layer 3: Disposable / Temporary Email Blocker
            $emailDomain = strtolower(substr(strrchr($validated['email'], "@"), 1));
            if (in_array($emailDomain, self::DISPOSABLE_EMAIL_DOMAINS)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Pendaftaran Toko Gratis dibatasi. Silakan gunakan alamat email utama/resmi Anda (seperti Gmail, Yahoo, Outlook, atau domain instansi).'
                ]);
            }

            // Layer 4: Gmail Dot & Plus Alias Normalization
            $inputNormalized = $this->normalizeEmail($validated['email']);
            $freeStores = TenantRegistration::where('plan', 'free')
                ->whereIn('status', ['paid', 'created'])
                ->get(['email']);

            $hasFreeStore = false;
            foreach ($freeStores as $store) {
                if ($this->normalizeEmail($store->email) === $inputNormalized) {
                    $hasFreeStore = true;
                    break;
                }
            }

            if ($hasFreeStore) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Alamat email ini sudah terdaftar untuk Toko Gratis lainnya. Untuk mendaftarkan toko tambahan, silakan pilih Paket Santai atau Paket Premium.'
                ]);
            }

            // Layer 5: WhatsApp Unique Limit (1 free trial store per WA number)
            $hasFreeWa = TenantRegistration::where('whatsapp', $waClean)
                ->where('plan', 'free')
                ->whereIn('status', ['paid', 'created'])
                ->exists();

            if ($hasFreeWa) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Nomor WhatsApp ini sudah terdaftar untuk Toko Gratis lainnya. Silakan gunakan nomor lain atau pilih Paket Santai/Premium.'
                ]);
            }

            // Hit Rate Limiter
            RateLimiter::hit($rateKey, 3600); // 1 hour cooldown
        }

        // Create tenant slug/subdomain
        $slug = Str::slug($validated['namaToko']);
        if (Tenant::where('id', $slug)->exists() || TenantRegistration::where('tenant_id', $slug)->where('status', 'paid')->exists()) {
            return response()->json(['status' => 'error', 'message' => 'Nama toko (subdomain) ini sudah terpakai. Silakan gunakan nama lain.']);
        }

        $amount = 0;
        if ($validated['paket'] === 'santai') {
            $amount = 50000;
        } elseif ($validated['paket'] === 'premium') {
            $amount = 150000;
        }

        // Generate unique professional invoice code
        $invoiceCode = 'INV-REG-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        // Generate dynamic secure 8-character password
        $plainPassword = Str::random(8);

        // Save Registration to Database
        $registration = TenantRegistration::create([
            'invoice_code' => $invoiceCode,
            'owner_name' => $validated['namaOwner'],
            'email' => $validated['email'],
            'password' => $plainPassword, // Store plain password temporarily
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
                Artisan::call('tenant:create', [
                    'name' => $validated['namaToko'],
                    '--type' => $registration->store_type,
                    '--domain' => $domainUrl,
                    '--plan' => 'free',
                ]);

                // Update User Email & Password di dalam Tenant
                $tenant = Tenant::find($slug);
                $tenant?->run(function () use ($registration, $plainPassword) {
                    User::firstOrCreate(
                        ['email' => $registration->email],
                        [
                            'name' => $registration->owner_name,
                            'password' => $plainPassword, // Set plain password (Laravel casts handles the hashing)
                            'role' => 'manager'
                        ]
                    );
                });

                // Securely hash the password inside the onboarding central database now that user is initialized
                $registration->update([
                    'status' => 'created',
                    'password' => Hash::make($plainPassword)
                ]);

                // Send Welcome Email
                $emailTitle = "Toko " . $registration->store_name . " Siap Digunakan!";
                $emailBody = "Halo $registration->owner_name,\n\nSelamat bergabung di Pakaiapp! Sistem kasir toko Anda ($registration->store_name) telah selesai disiapkan.\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://$domainUrl/auth/login\nEmail: $registration->email\nPassword: $plainPassword\n\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n\nSalam sukses,\nTim Pakaiapp";

                Mail::to($registration->email)->send(
                    new SystemEmail($emailTitle, $emailBody, 'Buka Dashboard', "https://$domainUrl/auth/login")
                );

                $cookie = cookie()->forever('pakaiapp_free_trial_claimed', '1');
                return response()->json([
                    'status' => 'success',
                    'message' => 'Toko berhasil dibuat! Anda akan dialihkan ke dashboard.',
                    'redirect_url' => 'https://' . $domainUrl . '/auth/login'
                ])->withCookie($cookie);
            } catch (Exception $e) {
                Log::error("Free registration failed: " . $e->getMessage());

                // Send Failure Email
                $emailTitle = "Pendaftaran Toko Gagal";
                $emailBody = "Halo $registration->owner_name,\n\nMohon maaf, terjadi kesalahan sistem saat menyiapkan toko Anda ($registration->store_name). Tim kami sedang menangani masalah ini.\n\nSilakan coba beberapa saat lagi atau hubungi support kami jika masalah berlanjut.\n\nSalam,\nTim Pakaiapp";

                try {
                    Mail::to($registration->email)->send(
                        new SystemEmail($emailTitle, $emailBody, 'Hubungi Support', "https://wa.me/6285172441544")
                    );
                } catch (Exception $mailEx) {
                    Log::error("Failed to send failure email: " . $mailEx->getMessage());
                }

                return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan sistem saat membuat toko.']);
            }
        }

        // Jika Manual (WA)
        if ($validated['payment_method'] === 'manual') {
            $text = "Halo Admin Pakaiapp, saya ingin mendaftar toko baru dengan rincian:\n"
                . "Nama Toko: $registration->store_name\n"
                . "Pemilik: $registration->owner_name\n"
                . "Email: $registration->email\n"
                . "Paket: " . ucfirst($registration->plan) . "\n"
                . "Invoice: $invoiceCode\n"
                . "Mohon info rekening untuk pembayaran sebesar Rp " . number_format($amount, 0, ',', '.') . ". Terima kasih.";

            $waUrl = "https://wa.me/6285172441544?text=" . urlencode($text);

            // Send Billing Invoice Email (Manual)
            $emailTitle = "Menunggu Pembayaran (Manual) - $invoiceCode";
            $emailBody = "Halo $registration->owner_name,\n\nPendaftaran toko Anda ($registration->store_name) telah kami catat dengan Paket " . ucfirst($registration->plan) . ".\n\nNomor Tagihan: $invoiceCode\nTotal Tagihan: Rp " . number_format($amount, 0, ',', '.') . "\nMetode: Transfer Manual\n\nSilakan klik tombol di bawah ini untuk chat dengan Admin kami guna mengkonfirmasi pembayaran Anda. Setelah dikonfirmasi, toko Anda akan langsung kami aktifkan.\n\nTerima kasih,\nTim Pakaiapp";

            Mail::to($registration->email)
                ->send((new SystemEmail($emailTitle, $emailBody, 'Konfirmasi via WA', $waUrl))
                    ->from('billing@pakaiapp.online', 'Pakaiapp Billing'));

            return response()->json([
                'status' => 'manual',
                'redirect_url' => $waUrl
            ]);
        }

        // Jika berbayar via Midtrans
        if ($validated['payment_method'] === 'midtrans') {
            try {
                $midtransService = app(MidtransService::class);
                $snapToken = $midtransService->createRegistrationSnapToken($registration);
                $registration->update(['snap_token' => $snapToken]);

                // Send Billing Invoice Email (Midtrans)
                $emailTitle = "Tagihan Pendaftaran Toko - $invoiceCode";
                $emailBody = "Halo $registration->owner_name,\n\nPendaftaran toko Anda ($registration->store_name) untuk Paket " . ucfirst($registration->plan) . " tinggal satu langkah lagi.\n\nNomor Tagihan: $invoiceCode\nTotal Tagihan: Rp " . number_format($amount, 0, ',', '.') . "\n\nSistem kami mendeteksi Anda akan menggunakan E-Wallet/QRIS. Silakan selesaikan pembayaran Anda di layar website Anda.\n\nTerima kasih,\nTim Pakaiapp";

                Mail::to($registration->email)
                    ->send((new SystemEmail($emailTitle, $emailBody))
                        ->from('billing@pakaiapp.online', 'Pakaiapp Billing'));

                return response()->json([
                    'status' => 'payment_required_midtrans',
                    'snap_token' => $snapToken,
                    'invoice_code' => $invoiceCode,
                ]);
            } catch (Exception $e) {
                Log::error("Gagal create snap token registrasi: " . $e->getMessage());
                return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke layanan pembayaran Midtrans.']);
            }
        }

        // Jika berbayar via Duitku
        try {
            $duitkuService = app(DuitkuService::class);
            $duitkuInvoice = $duitkuService->createRegistrationInvoice($registration, $validated['payment_method']);

            $registration->update([
                'duitku_payment_url' => $duitkuInvoice['payment_url'],
                'duitku_reference' => $duitkuInvoice['reference']
            ]);

            // Send Billing Invoice Email (Duitku)
            $emailTitle = "Tagihan Pendaftaran Toko - $invoiceCode";
            $emailBody = "Halo $registration->owner_name,\n\nPendaftaran toko Anda ($registration->store_name) untuk Paket " . ucfirst($registration->plan) . " telah diteruskan ke Payment Gateway.\n\nNomor Tagihan: $invoiceCode\nTotal Tagihan: Rp " . number_format($amount, 0, ',', '.') . "\n\nJika halaman pembayaran tidak terbuka otomatis atau tertutup, silakan klik tombol di bawah ini untuk melanjutkan pembayaran Anda.\n\nSetelah pembayaran berhasil, toko Anda akan otomatis disiapkan.\n\nTerima kasih,\nTim Pakaiapp";

            Mail::to($registration->email)
                ->send((new SystemEmail($emailTitle, $emailBody, 'Lanjutkan Pembayaran', $duitkuInvoice['payment_url']))
                    ->from('billing@pakaiapp.online', 'Pakaiapp Billing'));

            return response()->json([
                'status' => 'payment_required_duitku',
                'payment_url' => $duitkuInvoice['payment_url'],
                'invoice_code' => $invoiceCode,
            ]);
        } catch (Exception $e) {
            Log::error("Gagal create Duitku invoice registrasi: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal terhubung ke layanan pembayaran Duitku.']);
        }
    }

    private function normalizeEmail(string $email): string
    {
        $email = strtolower(trim($email));
        if (str_contains($email, '@gmail.com') || str_contains($email, '@googlemail.com')) {
            [$username, $domain] = explode('@', $email);
            $username = explode('+', $username)[0]; // Remove Gmail alias (+something)
            $username = str_replace('.', '', $username); // Remove Gmail dots
            return $username . '@gmail.com';
        }
        return $email;
    }
}

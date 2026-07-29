<?php

namespace App\Central\Services;

use App\Central\Data\CentralLoginResultData;
use App\Central\Data\RegisterStatusData;
use App\Central\Data\RegistrationResultData;
use App\Central\Models\Tenant;
use App\Central\Models\TenantRegistration;
use App\Central\Models\User;
use App\Shared\Mail\SystemEmail;
use DomainException;
use Exception;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Random\RandomException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class TenantRegistrationService
{
    protected ?RegistrationAbuseGuardService $registrationAbuseGuardService = null;

    protected ?MidtransService $midtransService = null;

    protected ?DuitkuService $duitkuService = null;

    protected function registrationAbuseGuardService(): RegistrationAbuseGuardService
    {
        return $this->registrationAbuseGuardService ??= app(RegistrationAbuseGuardService::class);
    }

    protected function midtransService(): MidtransService
    {
        return $this->midtransService ??= app(MidtransService::class);
    }

    protected function duitkuService(): DuitkuService
    {
        return $this->duitkuService ??= app(DuitkuService::class);
    }

    /**
     * Mengambil status pendaftaran tenant dan return object RegisterStatusData
     */
    public function getRegisterStatus(string $invoiceCode): RegisterStatusData
    {
        $registration = TenantRegistration::where('invoice_code', $invoiceCode)->first();
        if (!$registration) throw new DomainException('Pendaftaran tidak ditemukan.', 404);

        $centralDomain = config('tenancy.central_domains')[2] ?? 'pakaiapp.online';
        $domainUrl = "https://$registration->tenant_id.$centralDomain/auth/login";

        if ($registration->status === 'created') {
            $autoLoginToken = Str::random(40);
            Cache::put(
                key: "auto_login_$autoLoginToken",
                value: $registration->email,
                ttl: now()->addMinutes(15)
            );
            $domainUrl = "https://$registration->tenant_id.$centralDomain/auth/auto-login?token=$autoLoginToken";
        }

        return new RegisterStatusData(
            payment_status: $registration->status,
            redirect_url: $domainUrl,
            payment_url: $registration->duitku_payment_url ?? null,
        );
    }

    /**
     * Memproses Central Login input (Email atau Subdomain)
     */
    public function processCentralLogin(string $input): CentralLoginResultData
    {
        // 1. Check if input is Email
        if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
            $registrations = TenantRegistration::where('email', $input)
                ->where('status', 'created')
                ->get();

            if ($registrations->isEmpty()) throw new DomainException(
                message: 'Email ini belum terdaftar atau toko belum selesai disiapkan.',
                code: ResponseAlias::HTTP_NOT_FOUND
            );

            return new CentralLoginResultData(
                type: 'email',
                stores: $registrations->map(function ($reg) {
                    $centralDomain = config('tenancy.central_domains')[2] ?? 'pakaiapp.online';
                    $domainUrl = "$reg->tenant_id.$centralDomain";

                    return [
                        'store_name' => $reg->store_name,
                        'tenant_id' => $reg->tenant_id,
                        'url' => "https://$domainUrl/auth/login",
                    ];
                })->values()
            );
        }

        // 2. Check if input is Subdomain / Shop name
        $slug = Str::slug($input);
        $tenant = Tenant::find($slug);
        if ($tenant) {
            $domain = $tenant->domains->first()?->domain ?? ($slug . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online'));

            return new CentralLoginResultData(
                type: 'subdomain',
                redirect_url: "https://$domain/auth/login"
            );
        }

        // 3. Check if shop is pending registration
        if (TenantRegistration::where('tenant_id', $slug)->exists()) throw new DomainException(
            message: 'Toko Anda sedang disiapkan atau menunggu pembayaran. Silakan cek status pendaftaran.',
            code: ResponseAlias::HTTP_FORBIDDEN
        );

        throw new DomainException(
            message: 'Toko atau Email tidak ditemukan. Harap periksa kembali.',
            code: ResponseAlias::HTTP_NOT_FOUND
        );
    }

    /**
     * @throws RandomException
     */
    public function requestOtp(string $email): void
    {
        // Check if there is already a verified token
        if (Cache::get('email_verified_' . $email)) throw new DomainException(
            message: 'Email ini sudah terverifikasi.',
            code: ResponseAlias::HTTP_BAD_REQUEST
        );
        // Generate 6 digit OTP
        $otp = str_pad((string)random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP in cache for 5 minutes
        Cache::put(' ' . $email, $otp, now()->addMinutes(5));

        // Send Email
        $resumeUrl = url('/register?resume_email=' . urlencode($email));
        $emailTitle = 'Kode Verifikasi (OTP) Pendaftaran';
        $emailBody = "Halo,\n" .
            "\nTerima kasih telah mendaftar di Pakaiapp. Berikut adalah kode OTP Anda untuk verifikasi email:\n" .
            "\n$otp\n" .
            "\nKode ini berlaku selama 5 menit. Jangan berikan kode ini kepada siapa pun.\n" .
            "\nJika halaman pendaftaran Anda tidak sengaja tertutup, Anda dapat mengklik tautan berikut untuk melanjutkannya:\n" .
            "$resumeUrl";

        try {
            Mail::to($email)->send(
                mailable: new SystemEmail(
                    title: $emailTitle,
                    messageContent: $emailBody,
                    callToActionText: 'Lanjutkan Pendaftaran',
                    callToActionUrl: $resumeUrl
                )
            );
        } catch (Exception $e) {
            Log::error('Failed to send OTP email: ' . $e->getMessage());
            throw new DomainException(
                message: 'Gagal mengirim email OTP. Silakan coba lagi.',
                code: ResponseAlias::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Verifikasi kode OTP
     */
    public function verifyOtp(string $email, string $otp): void
    {
        $cachedOtp = Cache::get('otp_register_' . $email);

        if (!$cachedOtp || $cachedOtp !== $otp) throw new DomainException(
            message: 'Kode OTP tidak valid atau sudah kedaluwarsa.',
            code: ResponseAlias::HTTP_BAD_REQUEST
        );

        // Mark email as verified for 30 minutes
        Cache::put(
            key: "email_verified_$email",
            value: true,
            ttl: now()->addMinutes(30)
        );

        // Remove OTP from cache
        Cache::forget(key: "otp_register_$email");
    }

    /**
     * Mengecek apakah email sudah diverifikasi via OTP
     */
    public function isEmailVerified(string $email): bool
    {
        return (bool)Cache::get("email_verified_$email");
    }

    /**
     * @throws ConnectionException
     * @throws Exception
     */
    public function initiateRegistration(array $data, string $ip, bool $hasFreeTrialCookie): RegistrationResultData
    {
        // 1. Sanitize WhatsApp
        $waClean = preg_replace('/[^0-9]/', '', $data['noWa']);
        if (str_starts_with($waClean, '0')) $waClean = '62' . substr($waClean, 1);

        // 2. FREE TRIAL ABUSE PREVENTION
        if ($data['paket'] === 'free') {
            $abuseError = $this->registrationAbuseGuardService()->check($ip, $hasFreeTrialCookie, $data['email'], $waClean);
            if ($abuseError) throw new DomainException($abuseError);
        }

        // 3. Create tenant slug/subdomain
        $slug = Str::slug($data['namaToko']);
        if (
            Tenant::where('id', $slug)->exists() ||
            TenantRegistration::where('tenant_id', $slug)->where('status', 'paid')->exists()
        ) throw new DomainException(message: 'Nama toko (subdomain) ini sudah terpakai. Silakan gunakan nama lain.');

        // 4. Generate Invoice
        $invoiceCode = 'INV-REG-' . date('Ymd') . '-' . strtoupper(Str::random(6));

        $amount = match ($data['paket']) {
            'santai' => 50000,
            'premium' => 150000,
            default => 0,
        };

        // 6. Save Registration to Database (simpan dummy password, password asli dibuat saat provision)
        $registration = TenantRegistration::create([
            'invoice_code' => $invoiceCode,
            'owner_name' => $data['namaOwner'],
            'email' => $data['email'],
            ' ' => Hash::make(Str::random(32)),
            'store_name' => $data['namaToko'],
            'store_type' => $data['jenisBisnis'] === 'F&B (Resto/Cafe)' ? 'resto' : 'retail',
            'tenant_id' => $slug,
            'whatsapp' => $waClean,
            'plan' => $data['paket'],
            'amount' => $amount,
            'status' => 'pending',
            'payment_method' => $data['payment_method'],
        ]);

        // 7. Jika gratis, langsung proses pembuatan tenant
        if ($amount == 0) {
            $result = $this->completeFreePlanRegistration($registration);

            return new RegistrationResultData(
                type: 'free',
                message: 'Toko berhasil dibuat! Anda akan dialihkan ke dashboard.',
                redirect_url: $result['redirect_url'],
            );
        }

        // 8. Jika Manual (WA)
        if ($data['payment_method'] === 'manual') {
            $text = "Halo Admin Pakaiapp, saya ingin mendaftar toko baru dengan rincian:\n"
                . "Nama Toko: $registration->store_name\n"
                . "Pemilik: $registration->owner_name\n"
                . "Email: $registration->email\n"
                . 'Paket: ' . ucfirst($registration->plan) . "\n"
                . "Invoice: $invoiceCode\n"
                . 'Mohon info rekening untuk pembayaran sebesar Rp ' . number_format($amount, 0, ',', '.') . '. Terima kasih.';

            $waUrl = 'https://wa.me/6285172441544?text=' . urlencode($text);

            $this->sendBillingEmail(
                registration: $registration,
                paymentMethod: 'manual',
                paymentData: ['wa_url' => $waUrl]
            );

            return new RegistrationResultData(
                type: 'manual',
                message: 'Silakan lanjutkan pembayaran via WhatsApp',
                redirect_url: $waUrl,
            );
        }

        // 9. Jika berbayar via Midtrans
        if ($data['payment_method'] === 'midtrans') {
            $midtransService = $this->midtransService();
            $snapToken = $midtransService->createRegistrationSnapToken($registration);
            $registration->update(['snap_token' => $snapToken]);

            $this->sendBillingEmail(registration: $registration, paymentMethod: 'midtrans');

            return new RegistrationResultData(
                type: 'midtrans',
                message: 'Silakan selesaikan pembayaran via Midtrans',
                snap_token: $snapToken,
                invoice_code: $invoiceCode,
            );
        }

        // 10. Jika berbayar via Duitku
        $duitkuService = $this->duitkuService();
        $duitkuInvoice = $duitkuService->createRegistrationInvoice($registration, $data['payment_method']);

        $registration->update([
            'duitku_payment_url' => $duitkuInvoice->paymentUrl,
            'duitku_reference' => $duitkuInvoice->reference,
        ]);

        $this->sendBillingEmail(
            registration: $registration,
            paymentMethod: 'duitku',
            paymentData: ['payment_url' => $duitkuInvoice->paymentUrl]
        );

        return new RegistrationResultData(
            type: 'duitku',
            message: 'Silakan selesaikan pembayaran via Duitku',
            payment_url: $duitkuInvoice->paymentUrl,
            invoice_code: $invoiceCode,
        );
    }

    /**
     * Selesaikan pendaftaran tenant (buat database, user, kirim email).
     */
    public function completeRegistration(TenantRegistration $registration): void
    {
        try {
            [$domainUrl, $plainPassword] = $this->setupTenantDatabase($registration);

            // Send Welcome Email
            $emailTitle = "Toko $registration->store_name Siap Digunakan!";
            $emailBody = "Halo $registration->owner_name,\n"
                . "\nTerima kasih atas pembayaran Anda! Sistem kasir toko Anda ($registration->store_name) telah selesai disiapkan dengan Paket "
                . ucfirst($registration->plan)
                . ".\n\nBerikut adalah detail akses Anda:\nURL Dashboard: https://$domainUrl/auth/login"
                . "\nEmail: $registration->email"
                . "\nPassword: $plainPassword \n"
                . "\nSilakan login untuk mulai mengatur menu dan memantau pesanan Anda.\n"
                . "\nSalam sukses,\nTim Pakaiapp";

            Mail::to($registration->email)->send(
                mailable: new SystemEmail(
                    title: $emailTitle,
                    messageContent: $emailBody,
                    callToActionText: 'Buka Dashboard',
                    callToActionUrl: "https://$domainUrl/auth/login"
                )
            );

            Log::info('[TenantRegistrationService] Tenant Registration Success', ['tenant_id' => $registration->tenant_id]);
        } catch (Exception $e) {
            $emailBody = "Halo $registration->owner_name,\n"
                . "\nTerima kasih atas pembayaran Anda. Namun, mohon maaf terjadi kesalahan sistem saat menyiapkan toko Anda (" . $registration->store_name . "). Tim kami sedang menelusuri masalah ini secara manual.\n"
                . "\nSilakan hubungi tim support kami dengan melampirkan email ini agar segera ditindaklanjuti.\n"
                . "\nSalam,\nTim Pakaiapp";
            $this->handleRegistrationFailure(
                registration: $registration,
                e: $e,
                emailBody: $emailBody,
                logMessage: '[TenantRegistrationService] Failed to create tenant after payment'
            );
        }
    }

    /**
     * Selesaikan pendaftaran untuk Paket Free secara langsung.
     * Mengembalikan URL redirect untuk login otomatis.
     *
     * @throws Exception
     */
    public function completeFreePlanRegistration(TenantRegistration $registration): array
    {
        try {
            [$domainUrl, $plainPassword] = $this->setupTenantDatabase($registration);

            $emailTitle = "Toko $registration->store_name Siap Digunakan!";
            $emailBody = "Halo $registration->owner_name,\n"
                . "\nSelamat bergabung di Pakaiapp! Sistem kasir toko Anda ($registration->store_name) telah selesai disiapkan.\n"
                . "\nBerikut adalah detail akses Anda:"
                . "\nURL Dashboard: https://$domainUrl/auth/login"
                . "\nEmail: $registration->email"
                . "\nPassword: $plainPassword"
                . "\n\nSilakan klik link URL Dashboard di atas untuk login. Jangan lupa untuk segera mengubah password Anda setelah berhasil login demi keamanan akun Anda.\n"
                . "\nSalam sukses,\nTim Pakaiapp";

            Mail::to($registration->email)->send(
                mailable: new SystemEmail(
                    title: $emailTitle,
                    messageContent: $emailBody,
                    callToActionText: 'Buka Dashboard',
                    callToActionUrl: "https://$domainUrl/auth/login"
                )
            );

            $autoLoginToken = Str::random(40);
            Cache::put(
                key: "auto_login_$autoLoginToken",
                value: $registration->email,
                ttl: now()->addMinutes(15)
            );

            return [
                'success' => true,
                'redirect_url' => "https://$domainUrl/auth/auto-login?token=$autoLoginToken",
            ];
        } catch (Exception $e) {
            $emailBody = "Halo $registration->owner_name,\n"
                . "\nMohon maaf, terjadi kesalahan sistem saat menyiapkan toko Anda ($registration->store_name)."
                . " Tim kami sedang menangani masalah ini.\n"
                . "\nSilakan coba beberapa saat lagi atau hubungi support kami jika masalah berlanjut.\n"
                . "\nSalam,\nTim Pakaiapp";

            $this->handleRegistrationFailure(
                registration: $registration,
                e: $e,
                emailBody: $emailBody,
                logMessage: '[TenantRegistrationService] Free registration failed'
            );

            throw $e;
        }
    }

    /**
     * Helper untuk menyiapkan database tenant dan manager user.
     * Mengembalikan domain URL dan plain password.
     *
     * @throws Exception
     */
    private function setupTenantDatabase(TenantRegistration $registration): array
    {
        $domainUrl = $registration->tenant_id . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');
        $plainPassword = Str::random(8); // Generate actual password securely here

        Artisan::call('tenant:create', [
            'name' => $registration->store_name,
            '--id' => $registration->tenant_id,
            '--type' => $registration->store_type,
            '--domain' => $domainUrl,
            '--plan' => $registration->plan,
        ]);

        $tenant = Tenant::find($registration->tenant_id);
        $tenant?->run(function () use ($registration, $plainPassword) {
            User::firstOrCreate(
                ['email' => $registration->email],
                [
                    'name' => $registration->owner_name,
                    'password' => $plainPassword, // Set plain password (Laravel casts handles the hashing)
                    'role' => 'manager',
                ]
            );
        });

        // Securely hash the password inside the central DB now that store is ready
        $registration->update([
            'status' => 'created',
            'password' => Hash::make($plainPassword),
        ]);

        return [$domainUrl, $plainPassword];
    }

    /**
     * Helper to log registration failure and send error email to user
     */
    private function handleRegistrationFailure(TenantRegistration $registration, Exception $e, string $emailBody, string $logMessage): void
    {
        Log::error($logMessage, ['error' => $e->getMessage()]);

        try {
            Mail::to($registration->email)->send(
                new SystemEmail('Pendaftaran Toko Gagal', $emailBody, 'Hubungi Support', 'https://wa.me/6285172441544')
            );
        } catch (Exception $mailEx) {
            Log::error('[TenantRegistrationService] Failed to send failure email: ' . $mailEx->getMessage());
        }
    }

    /**
     * Kirim email invoice pembayaran berdasarkan metode pembayaran.
     */
    public function sendBillingEmail(TenantRegistration $registration, string $paymentMethod, array $paymentData = []): void
    {
        $amountFmt = number_format((float)$registration->amount, 0, ',', '.');

        try {
            $emailTitle = '';
            $emailBody = '';
            $btnText = null;
            $btnUrl = null;

            if ($paymentMethod === 'manual') {
                $btnUrl = $paymentData['wa_url'] ?? '#';
                $btnText = 'Konfirmasi via WA';
                $emailTitle = 'Menunggu Pembayaran (Manual) - ' . $registration->invoice_code;
                $emailBody = 'Halo ' . $registration->owner_name . ",\n\nPendaftaran toko Anda (" . $registration->store_name . ') telah kami catat dengan Paket ' . ucfirst($registration->plan) . ".\n\nNomor Tagihan: " . $registration->invoice_code . "\nTotal Tagihan: Rp " . $amountFmt . "\nMetode: Transfer Manual\n\nSilakan klik tombol di bawah ini untuk chat dengan Admin kami guna mengkonfirmasi pembayaran Anda. Setelah dikonfirmasi, toko Anda akan langsung kami aktifkan.\n\nTerima kasih,\nTim Pakaiapp";
            } elseif ($paymentMethod === 'midtrans') {
                $emailTitle = 'Tagihan Pendaftaran Toko - ' . $registration->invoice_code;
                $emailBody = 'Halo ' . $registration->owner_name . ",\n\nPendaftaran toko Anda (" . $registration->store_name . ') untuk Paket ' . ucfirst($registration->plan) . " tinggal satu langkah lagi.\n\nNomor Tagihan: " . $registration->invoice_code . "\nTotal Tagihan: Rp " . $amountFmt . "\n\nSistem kami mendeteksi Anda akan menggunakan E-Wallet/QRIS. Silakan selesaikan pembayaran Anda di layar website Anda.\n\nTerima kasih,\nTim Pakaiapp";
            } elseif ($paymentMethod === 'duitku') {
                $btnUrl = $paymentData['payment_url'] ?? '#';
                $btnText = 'Lanjutkan Pembayaran';
                $emailTitle = 'Tagihan Pendaftaran Toko - ' . $registration->invoice_code;
                $emailBody = 'Halo ' . $registration->owner_name . ",\n\nPendaftaran toko Anda (" . $registration->store_name . ') untuk Paket ' . ucfirst($registration->plan) . " telah diteruskan ke Payment Gateway.\n\nNomor Tagihan: " . $registration->invoice_code . "\nTotal Tagihan: Rp " . $amountFmt . "\n\nJika halaman pembayaran tidak terbuka otomatis atau tertutup, silakan klik tombol di bawah ini untuk melanjutkan pembayaran Anda.\n\nSetelah pembayaran berhasil, toko Anda akan otomatis disiapkan.\n\nTerima kasih,\nTim Pakaiapp";
            }

            if ($emailTitle) {
                Mail::to($registration->email)
                    ->send((new SystemEmail($emailTitle, $emailBody, $btnText, $btnUrl))
                        ->from('billing@pakaiapp.online', 'Pakaiapp Billing'));
            }
        } catch (Exception $e) {
            Log::error('[TenantRegistrationService] Failed to send billing email', [
                'invoice_code' => $registration->invoice_code,
                'error' => $e->getMessage(),
            ]);
            // We intentionally don't throw DomainException here so that the registration process
            // can still succeed even if the email failed to send (e.g. SMTP issue).
        }
    }
}

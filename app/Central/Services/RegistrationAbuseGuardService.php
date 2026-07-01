<?php

namespace App\Central\Services;

use App\Central\Models\TenantRegistration;
use Illuminate\Support\Facades\RateLimiter;

class RegistrationAbuseGuardService
{
    private const array DISPOSABLE_EMAIL_DOMAINS = [
        'yopmail.com', 'mailinator.com', 'tempmail.com', '10minutemail.com',
        'sharklasers.com', 'guerrillamail.com', 'dispostable.com', 'getairmail.com',
        'maildrop.cc', 'temp-mail.org', 'fakeinbox.com', 'throwawaymail.com',
        'mailnesia.com', 'mailcatch.com', 'yopmail.fr', 'yopmail.net',
        'cool.fr.nf', 'jetable.org', 'boun.cr', 'trbvm.com'
    ];

    /**
     * Mengecek apakah ada indikasi abuse pada pendaftaran Free Plan.
     * Mengembalikan pesan error (string) jika terdeteksi, atau null jika aman.
     */
    public function check(string $ip, bool $hasFreeTrialCookie, string $email, string $whatsapp): ?string
    {
        // Layer 1: IP Rate Limiting (max 2 free registrations per hour per IP)
        $rateKey = 'free_registration_limit_' . $ip;
        if (RateLimiter::tooManyAttempts($rateKey, 2)) {
            $seconds = RateLimiter::availableIn($rateKey);
            return 'Batas pendaftaran toko gratis terlampaui untuk perangkat Anda. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.';
        }

        // Layer 2: Cookie Fingerprint Protection
        if ($hasFreeTrialCookie) {
            return 'Perangkat/Browser Anda terdeteksi sudah pernah mendaftarkan Toko Gratis. Untuk mendaftarkan toko tambahan, silakan pilih Paket Santai atau Paket Premium.';
        }

        // Layer 3: Disposable / Temporary Email Blocker
        $emailDomain = strtolower(substr(strrchr($email, "@"), 1));
        if (in_array($emailDomain, self::DISPOSABLE_EMAIL_DOMAINS)) {
            return 'Pendaftaran Toko Gratis dibatasi. Silakan gunakan alamat email utama/resmi Anda (seperti Gmail, Yahoo, Outlook, atau domain instansi).';
        }

        // Layer 4: Gmail Dot & Plus Alias Normalization
        $inputNormalized = $this->normalizeEmail($email);
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
            return 'Alamat email ini sudah terdaftar untuk Toko Gratis lainnya. Untuk mendaftarkan toko tambahan, silakan pilih Paket Santai atau Paket Premium.';
        }

        // Layer 5: WhatsApp Unique Limit (1 free trial store per WA number)
        $hasFreeWa = TenantRegistration::where('whatsapp', $whatsapp)
            ->where('plan', 'free')
            ->whereIn('status', ['paid', 'created'])
            ->exists();

        if ($hasFreeWa) {
            return 'Nomor WhatsApp ini sudah terdaftar untuk Toko Gratis lainnya. Silakan gunakan nomor lain atau pilih Paket Santai/Premium.';
        }

        // Jika semua lolos, catat ke limiter
        RateLimiter::hit($rateKey, 3600); // 1 hour cooldown

        return null;
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

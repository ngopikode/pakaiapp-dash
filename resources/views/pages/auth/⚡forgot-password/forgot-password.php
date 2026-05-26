<?php

use App\Models\User;
use App\Models\StoreSetting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\SystemEmail;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::guest', ['title' => 'Lupa Password - Pakaiapp'])]
class extends Component {
    public string $email = '';
    public string $statusMessage = '';
    public bool $isSuccess = false;

    public function sendResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        $normalizedEmail = strtolower(trim($this->email));

        // 1. Anti-Spam Rate Limiting (Throttle request to max 1 per 2 minutes per email)
        $existingToken = DB::table('password_reset_tokens')
            ->where('email', $normalizedEmail)
            ->first();

        if ($existingToken && now()->parse($existingToken->created_at)->addMinutes(2)->isFuture()) {
            $secondsRemaining = now()->parse($existingToken->created_at)->addMinutes(2)->diffInSeconds(now());
            $this->addError('email', "Silakan tunggu {$secondsRemaining} detik sebelum mengirim ulang email pemulihan.");
            return;
        }

        // 2. Generate cryptographically secure random token (plain text for URL)
        $plainToken = Str::random(64);
        // Hashing for database security (OWASP A02:2021)
        $hashedToken = hash('sha256', $plainToken);

        // 3. User Lookup (Specific to this tenant connection)
        $user = User::where('email', $normalizedEmail)->first();

        if ($user) {
            // Delete old tokens first
            DB::table('password_reset_tokens')->where('email', $normalizedEmail)->delete();

            // Insert new token (secure hash)
            DB::table('password_reset_tokens')->insert([
                'email' => $normalizedEmail,
                'token' => $hashedToken,
                'created_at' => now(),
            ]);

            // Construct secure recovery link
            $storeName = $this->settings ? $this->settings->name : 'Toko POS Anda';
            $domainUrl = tenant('id') . '.' . (config('tenancy.central_domains')[2] ?? 'pakaiapp.online');
            $resetUrl = "https://{$domainUrl}/auth/reset-password?token={$plainToken}&email=" . urlencode($normalizedEmail);

            // Send premium welcome-like recovery email
            $emailTitle = "Atur Ulang Kata Sandi - {$storeName}";
            $emailBody = "Halo {$user->name},\n\nKami menerima permintaan untuk mengatur ulang kata sandi akun kasir Anda di {$storeName}.\n\nSilakan klik tombol di bawah ini untuk mengatur kata sandi baru Anda. Tautan ini akan kedaluwarsa dalam 15 menit.\n\nJika Anda tidak meminta pengaturan ulang kata sandi ini, silakan abaikan email ini.\n\nSalam,\nTim Pakaiapp";

            try {
                Mail::to($normalizedEmail)->send(
                    new SystemEmail($emailTitle, $emailBody, 'Atur Ulang Password', $resetUrl)
                );
            } catch (\Exception $e) {
                Log::error("Failed to send password reset email: " . $e->getMessage());
                $this->addError('email', 'Gagal mengirim email pemulihan. Silakan hubungi admin.');
                return;
            }
        }

        // 4. User Enumeration Defense: Always show a success message!
        $this->isSuccess = true;
        $this->statusMessage = "Tautan pemulihan kata sandi telah dikirimkan ke email Anda. Silakan cek kotak masuk atau folder spam email Anda.";
    }

    #[Computed]
    public function settings(): ?StoreSetting
    {
        return StoreSetting::first();
    }
};

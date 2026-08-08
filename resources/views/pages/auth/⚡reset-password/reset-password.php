<?php

use App\Central\Models\User;
use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::guest', ['title' => 'Atur Ulang Password - Pakaiapp'])]
class extends Component
{
    public string $token = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public string $statusMessage = '';

    public bool $isSuccess = false;

    public function mount(): void
    {
        $this->token = request()->query('token', '');
        $this->email = request()->query('email', '');
    }

    public function resetPassword(): void
    {
        $this->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $normalizedEmail = strtolower(trim($this->email));

        // 1. Verify token record exists
        $record = DB::table('password_reset_tokens')
            ->where('email', $normalizedEmail)
            ->first();

        if (!$record) {
            $this->addError('email', 'Permintaan atur ulang kata sandi tidak ditemukan atau email tidak valid.');

            return;
        }

        // 2. Hash the incoming URL plain token and compare to DB (SHA-256)
        $hashedInputToken = hash('sha256', $this->token);
        if ($record->token !== $hashedInputToken) {
            $this->addError('email', 'Tautan pemulihan kata sandi tidak valid.');

            return;
        }

        // 3. Expiration window gate (OWASP: expires after 15 minutes)
        if (now()->parse($record->created_at)->addMinutes(15)->isPast()) {
            $this->addError('email', 'Tautan pemulihan kata sandi sudah kedaluwarsa. Silakan ajukan permintaan baru.');

            return;
        }

        // 4. Retrieve User in the active Tenant context
        $user = User::where('email', $normalizedEmail)->first();
        if (!$user) {
            $this->addError('email', 'Pengguna tidak ditemukan.');

            return;
        }

        // 5. Update user password
        $user->update([
            'password' => Hash::make($this->password),
        ]);

        // 6. Delete used token immediately (Scrub token registry)
        DB::table('password_reset_tokens')->where('email', $normalizedEmail)->delete();

        // 7. Security: Invalidate all other active sessions for session fixation prevention
        session()->regenerate();

        $this->isSuccess = true;
        $this->statusMessage = 'Kata sandi Anda berhasil diperbarui! Silakan kembali ke halaman login untuk masuk ke aplikasi.';
    }

    #[Computed]
    public function settings(): ?StoreSetting
    {
        return StoreSetting::cached();
    }
};

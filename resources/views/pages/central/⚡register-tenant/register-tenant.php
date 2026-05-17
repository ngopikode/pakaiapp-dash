<?php

use App\Models\StoreSetting;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::central', ['title' => 'Posts Dashboard'])]
class extends Component {

    public ?string $userName;
    public ?string $userEmail;
    public ?string $password;
    public ?string $storeName;
    public string $storeType = 'resto'; // Default type
    public ?string $tenantId;
    public ?string $pin;

    public function createTenant(): void
    {
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email',
            'password' => 'required|min:6',
            'storeName' => 'required|string|max:255',
            'storeType' => 'required|in:resto,retail',
            'tenantId' => 'required|string|unique:tenants,id|alpha_dash',
            'pin' => 'required',
        ], [
            'tenantId.unique' => 'Subdomain ini sudah dipakai toko lain, Bro.',
            'tenantId.alpha_dash' => 'Subdomain cuma boleh huruf, angka, strip, atau underscore.',
        ]);

        if ($this->pin !== '260501') { // Ganti PIN rahasia lu di sini
            $this->addError('pin', 'PIN Pendaftaran salah! Hubungi Admin.');
            return;
        }

        $domainUrl = $this->tenantId . '.' . config('tenancy.central_domains')[2];

        // 1. Panggil Artisan Command yang baru
        $exitCode = Artisan::call('tenant:create', [
            'name' => $this->storeName,
            '--type' => $this->storeType,
            '--domain' => $domainUrl,
        ]);

        // Pastikan command sukses dijalankan (exit code 0 = sukses)
        if ($exitCode !== 0) {
            $this->addError('tenantId', 'Gagal mengeksekusi sistem, coba lagi nanti.');
            return;
        }

        // 2. Ambil tenant yang baru saja dibuat
        $tenant = Tenant::find($this->tenantId);

        $tenant?->run(function () {
            // Insert User Manager (Command artisan tidak membuat user, jadi kita buat di sini)
            User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($this->password),
                'role' => 'manager',
            ]);

            // Update Default Store Setting (Command artisan sudah bikin base-nya, kita tambahkan sisanya)
            $setting = StoreSetting::first();
            $setting?->update([
                'navbar_brand_text' => $this->storeName,
                'hero_headline' => 'Selamat datang di ' . $this->storeName,
            ]);
        });

        // 3. Redirect ke halaman login toko yang baru dibuat
        $protocol = request()->secure() ? 'https://' : 'http://';
        $this->redirect($protocol . $domainUrl . '/dashboard');
    }

    // Biar tenantId otomatis keisi pas lu ngetik nama toko (kayak slug)
    public function updatedStoreName($value): void
    {
        $this->tenantId = Str::slug($value);
    }
};

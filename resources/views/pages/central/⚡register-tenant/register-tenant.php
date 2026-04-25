<?php

use App\Models\StoreSetting;
use App\Models\Tenant;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::central', ['title' => 'Posts Dashboard'])]
class extends Component {
    // Data User
    public ?string $userName;

    public ?string $userEmail;

    public ?string $password;

    // Data Toko
    public ?string $storeName;

    public ?string $tenantId; // Untuk subdomain

    // Keamanan Cepat
    public ?string $pin;

    public function createTenant(): void
    {
        // 1. Validasi Input Dasar
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email',
            'password' => 'required|min:6',
            'storeName' => 'required|string|max:255',
            'tenantId' => 'required|string|unique:tenants,id|alpha_dash',
            'pin' => 'required',
        ], [
            'tenantId.unique' => 'Subdomain ini sudah dipakai toko lain, Bro.',
            'tenantId.alpha_dash' => 'Subdomain cuma boleh huruf, angka, strip, atau underscore.',
        ]);

        // 2. Cek PIN Hardcode
        if ($this->pin !== '260501') { // Ganti PIN rahasia lu di sini
            $this->addError('pin', 'PIN Pendaftaran salah! Hubungi Admin.');

            return;
        }

        // 3. Buat Tenant di Database Central
        $tenant = Tenant::create(['id' => $this->tenantId]);

        // 4. Buat Domain (Otomatis nyambung ke config sentral lu, misal .pakaiapp.dep)
        $domainUrl = $this->tenantId . '.' . config('tenancy.central_domains')[2];
        $tenant->domains()->create(['domain' => $domainUrl]);

        // 5. Nyebrang ke Database Tenant (Toko yang baru dibuat) buat ngisi User & Setting
        $tenant->run(function () {
            // Insert User Manager
            User::create([
                'name' => $this->userName,
                'email' => $this->userEmail,
                'password' => Hash::make($this->password),
                'role' => 'manager',
            ]);

            // Insert Default Store Setting
            StoreSetting::create([
                'name' => $this->storeName,
                'navbar_brand_text' => $this->storeName,
                'hero_headline' => 'Selamat datang di ' . $this->storeName,
                // Kolom lain otomatis ngikutin default value dari migrasi lu
            ]);
        });

        // 6. Redirect ke halaman login toko yang baru dibuat
        $protocol = request()->secure() ? 'https://' : 'http://';

        $this->redirect($protocol . $domainUrl . '/dashboard');
    }

    // Biar tenantId otomatis keisi pas lu ngetik nama toko (kayak slug)
    public function updatedStoreName($value): void
    {
        $this->tenantId = Str::slug($value);
    }
};

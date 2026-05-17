<?php

use Livewire\Component;
use App\Models\StoreSetting;

/**
 * StorePage — komponen induk halaman toko.
 *
 * Mirip seperti MenuPage.jsx di React:
 * - Loading awal (isLoading = true) → tampilkan LoadingSpinner fullscreen
 * - Error / store tidak ditemukan → tampilkan Error state
 * - Setelah store siap → render header-hero + product-list
 */
new class extends Component
{
    public bool   $isLoading  = true;
    public bool   $hasError   = false;
    public string $errorMsg   = '';
    public ?array $store      = null;

    public function mount(): void
    {
        $setting = StoreSetting::first();

        if (! $setting) {
            $this->hasError  = true;
            $this->isLoading = false;
            $this->errorMsg  = 'Toko tidak ditemukan atau belum dikonfigurasi.';
            return;
        }

        $this->store = [
            'name'        => $setting->name,
            'theme_color' => $setting->theme_color ?? '#f59e0b',
            'logo'        => $setting->logo,
        ];

        $this->isLoading = false;
    }
};
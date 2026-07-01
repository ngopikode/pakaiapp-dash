<?php

use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

new class extends Component {
    public string $elementId = 'sidebar-wrapper';

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('login');
    }

    public function getMenuSectionsProperty(): array
    {
        $user = Auth::user();
        $storeType = StoreSetting::first()?->store_type ?? 'retail';

        $sections = [
            [
                'title' => 'Menu Utama',
                'items' => [
                    ['route' => 'dashboard', 'icon' => 'bi bi-grid-fill', 'label' => 'Dashboard', 'roles' => ['manager']],
                    ['route' => 'cashier', 'icon' => 'bi bi-cash-coin', 'label' => 'Kasir / POS', 'roles' => ['manager', 'cashier']],
                    ['route' => 'order', 'icon' => 'bi bi-receipt-cutoff', 'label' => $storeType === 'resto' ? 'Pesanan & Riwayat' : 'Riwayat Transaksi', 'roles' => ['manager', 'cashier']],
                ]
            ],
            [
                'title' => 'Katalog & Inventaris',
                'items' => [
                    ['route' => 'product', 'icon' => 'bi bi-journal-richtext', 'label' => 'Katalog Produk', 'roles' => ['manager']],
                    ['route' => 'product-slot.buy', 'icon' => 'bi bi-cart-plus', 'label' => 'Beli Slot Produk', 'roles' => ['manager']],
                ]
            ],
            [
                'title' => 'Sistem & Pengaturan',
                'items' => [
                    ['route' => 'wallet', 'icon' => 'bi bi-wallet2', 'label' => 'Dompet & Saldo', 'roles' => ['manager']],
                    ['route' => 'store-setting', 'icon' => 'bi bi-shop', 'label' => 'Pengaturan Toko', 'roles' => ['manager']],
                    ['route' => 'user', 'icon' => 'bi bi-people', 'label' => 'Manajemen Pengguna', 'roles' => ['manager']],
                    ['route' => 'profile', 'icon' => 'bi bi-person-gear', 'label' => 'Profil Akun', 'roles' => ['manager', 'cashier']],
                    ['route' => 'ai-engine', 'icon' => 'bi bi-stars', 'label' => 'AI Menu Engine', 'roles' => ['manager']],
                ]
            ]
        ];

        if ($storeType === 'resto') {
            // Add Kitchen Screen to Menu Utama
            $sections[0]['items'][] = ['route' => 'kitchen', 'icon' => 'bi bi-display', 'label' => 'Layar Dapur (Kitchen)', 'roles' => ['manager', 'kitchen']];

            // Add Bahan Baku to Katalog & Inventaris
            $sections[1]['items'][] = ['route' => 'raw-material', 'icon' => 'bi bi-box-seam', 'label' => 'Bahan Baku & Resep', 'roles' => ['manager']];
        }

        // Filter Menu berdasarkan Role
        return collect($sections)->map(function ($section) use ($user) {
            $section['items'] = collect($section['items'])->filter(function ($item) use ($user) {
                // Jika user manager, bisa lihat semua. Jika cashier, hanya yang ada di list roles-nya.
                return in_array($user->role, $item['roles']);
            })->toArray();

            return $section;
        })->filter(fn($section) => count($section['items']) > 0)->toArray();
    }
};

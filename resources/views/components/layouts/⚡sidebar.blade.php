<?php

use App\Models\StoreSetting;
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
?>

<aside id="{{ $elementId }}">

    @if($elementId !== 'mobile-sidebar-wrapper')
    <div class="sidebar-heading px-4 py-3 border-bottom d-flex align-items-center"
         style="border-color: var(--bs-border-color) !important; min-height: 70px;">
        <h5 class="m-0 font-serif fw-bolder fs-5" style="letter-spacing: -0.02em; color: var(--brand-caramel, #B67332);">
            {{ \App\Models\StoreSetting::value('navbar_brand_text') ?? 'Navigasi Toko' }}
        </h5>
    </div>
    @endif

    <nav class="list-group list-group-flush my-3 flex-grow-1">
        @foreach($this->menuSections as $section)
            <div class="small text-secondary fw-bold px-4 mb-2 mt-3 text-uppercase"
                 style="font-size: 0.65rem; letter-spacing: 0.8px; color: var(--brand-caramel, #B67332) !important;">
                {{ $section['title'] }}
            </div>

            @foreach($section['items'] as $item)
                <x-layouts.sidebar-item
                    :route="$item['route']"
                    :icon="$item['icon']"
                    :label="$item['label']"
                    :active-route="request()->route()->getName()"
                />
            @endforeach
        @endforeach
    </nav>

    <div class="p-3 border-top" style="border-color: var(--bs-border-color) !important;">
        <style>
            /* Hide the install button if the app is already installed (standalone mode) */
            @media (display-mode: standalone) {
                .sidebar-pwa-install-btn { display: none !important; }
            }
        </style>
        <button type="button" onclick="if(window.installPwa) window.installPwa()"
                class="sidebar-pwa-install-btn btn btn-outline-success w-100 border-0 align-items-center justify-content-center gap-2 py-2.5 rounded-3 fw-bold transition-all shadow-sm mb-2"
                style="display: none; background-color: rgba(34, 197, 94, 0.08); border: 1px solid rgba(34, 197, 94, 0.2) !important; color: #16a34a; font-size: 0.88rem;">
            <i class="bi bi-download"></i> Install App
        </button>

        <button type="button" wire:click="logout"
                class="btn btn-outline-danger w-100 border-0 d-flex align-items-center justify-content-center gap-2 py-2.5 rounded-3 fw-bold transition-all shadow-sm"
                style="background-color: rgba(220, 53, 69, 0.04); border: 1px solid rgba(220, 53, 69, 0.1) !important; color: #dc3545; font-size: 0.88rem;">
            <i class="bi bi-box-arrow-left"></i> Log Out
        </button>
    </div>
</aside>

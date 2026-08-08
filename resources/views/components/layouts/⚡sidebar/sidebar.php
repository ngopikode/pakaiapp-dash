<?php

use App\Shared\Traits\ShowsToast;
use App\Tenant\Models\Core\Shift;
use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component
{
    use ShowsToast;

    public string $elementId = 'sidebar-wrapper';

    #[On('settings-updated')]
    public function refreshSidebar(): void
    {
        // No-op, livewire will just re-render this component.
    }

    public function logout(): void
    {
        $hasActiveShift = Shift::where('user_id', Auth::id())
            ->where('status', Shift::STATUS_ACTIVE)
            ->exists();

        if ($hasActiveShift) {
            $this->toast('Tutup shift kasir terlebih dahulu sebelum logout.', 'warning');

            return;
        }

        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('login');
    }

    public function getMenuSectionsProperty(): array
    {
        $user = Auth::user();
        $storeSetting = StoreSetting::cached();
        $storeType = $storeSetting?->store_type ?? 'retail';
        $isKitchenActive = (bool) ($storeSetting?->is_kitchen_active ?? true);
        $isDirectWa = $storeSetting?->isWaCheckoutActive() ?? false;
        $isPreorder = $storeSetting?->isPreorderActive() ?? false;

        $sections = [
            [
                'title' => 'Menu Utama',
                'items' => [
                    ['route' => 'dashboard', 'icon' => 'ph-fill ph-squares-four', 'label' => 'Dashboard', 'roles' => ['manager']],
                    ...(!$isDirectWa ? [['route' => 'cashier', 'icon' => 'ph-fill ph-cash-register', 'label' => 'Kasir / POS', 'roles' => ['manager', 'cashier']]] : []),
                    ['route' => 'order', 'icon' => 'ph-fill ph-receipt', 'label' => $storeType === 'resto' ? 'Pesanan & Riwayat' : 'Riwayat Transaksi', 'roles' => ['manager', 'cashier']],
                    ...($isPreorder ? [
                        ['route' => 'pre-order', 'icon' => 'ph-fill ph-package', 'label' => 'Pesanan Terjadwal', 'roles' => ['manager']],
                        ['route' => 'pre-order.recap', 'icon' => 'ph-fill ph-chart-bar', 'label' => 'Rekap Belanja Pasar', 'roles' => ['manager']],
                    ] : []),
                ],
            ],
            [
                'title' => 'Katalog & Inventaris',
                'items' => [
                    ['route' => 'products', 'icon' => 'ph-fill ph-book-open', 'label' => 'Katalog Produk', 'roles' => ['manager']],
                    ['route' => 'product-slot.buy', 'icon' => 'ph-fill ph-shopping-cart-simple', 'label' => 'Beli Slot Produk', 'roles' => ['manager']],
                ],
            ],
            [
                'title' => 'Keuangan',
                'items' => [
                    ['route' => 'cashbook', 'icon' => 'ph-fill ph-book-open-text', 'label' => 'Buku Kas', 'roles' => ['manager', 'cashier']],
                    ['route' => 'wallet', 'icon' => 'ph-fill ph-wallet', 'label' => 'Dompet & Saldo', 'roles' => ['manager']],
                ],
            ],
            [
                'title' => 'Sistem & Pengaturan',
                'items' => [
                    ['route' => 'store-setting', 'icon' => 'ph-fill ph-storefront', 'label' => 'Pengaturan Toko', 'roles' => ['manager']],
                    ['route' => 'user', 'icon' => 'ph-fill ph-users', 'label' => 'Manajemen Pengguna', 'roles' => ['manager']],
                    ['route' => 'profile', 'icon' => 'ph-fill ph-user-gear', 'label' => 'Profil Akun', 'roles' => ['manager', 'cashier']],
                    ['route' => 'ai-engine', 'icon' => 'ph-fill ph-sparkle', 'label' => 'AI Menu Engine', 'roles' => ['manager']],
                ],
            ],
        ];

        if ($storeType === 'resto' && $isKitchenActive && !$isDirectWa) {
            // Add Kitchen Screen to Menu Utama
            $sections[0]['items'][] = ['route' => 'kitchen', 'icon' => 'ph-fill ph-monitor', 'label' => 'Layar Dapur (Kitchen)', 'roles' => ['manager', 'kitchen']];

            // Add Bahan Baku to Katalog & Inventaris
            $sections[1]['items'][] = ['route' => 'raw-material', 'icon' => 'ph-fill ph-package', 'label' => 'Bahan Baku & Resep', 'roles' => ['manager']];
        }

        if ($isPreorder) {
            $sections[3]['items'][] = ['route' => 'delivery-zones.index', 'icon' => 'ph-fill ph-map-pin', 'label' => 'Area & Tarif Pengiriman', 'roles' => ['manager']];
            $sections[3]['items'][] = ['route' => 'delivery-slots.index', 'icon' => 'ph-fill ph-clock', 'label' => 'Slot Waktu Kirim', 'roles' => ['manager']];
        }

        // Filter Menu berdasarkan Role
        return collect($sections)->map(function ($section) use ($user) {
            $section['items'] = collect($section['items'])->filter(function ($item) use ($user) {
                // Jika user manager, bisa lihat semua. Jika cashier, hanya yang ada di list roles-nya.
                return in_array($user->role, $item['roles']);
            })->toArray();

            return $section;
        })->filter(fn ($section) => count($section['items']) > 0)->toArray();
    }
};

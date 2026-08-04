<?php

use App\Shared\Traits\ShowsToast;
use App\Tenant\Models\Core\Shift;
use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Pengaturan')]
class extends Component
{
    use ShowsToast;

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
        $storeType = StoreSetting::first()?->store_type ?? 'retail';

        $sections = [
            [
                'title' => 'Katalog & Inventaris',
                'items' => [
                    ['route' => 'product', 'icon' => 'ph-fill ph-book-open-text', 'label' => 'Katalog Produk', 'roles' => ['manager']],
                    ['route' => 'product-slot.buy', 'icon' => 'ph-fill ph-shopping-cart', 'label' => 'Beli Slot Produk', 'roles' => ['manager'], 'badge' => 'Baru'],
                ],
            ],
            [
                'title' => 'Keuangan',
                'items' => [
                    ['route' => 'buku-kas', 'icon' => 'ph-fill ph-book-open-text', 'label' => 'Buku Kas', 'roles' => ['manager', 'cashier']],
                    ['route' => 'wallet', 'icon' => 'ph-fill ph-wallet', 'label' => 'Dompet & Saldo', 'roles' => ['manager']],
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
                ],
            ],
        ];

        if ($storeType === 'resto') {
            $sections[0]['items'][] = ['route' => 'raw-material', 'icon' => 'ph-fill ph-package', 'label' => 'Bahan Baku & Resep', 'roles' => ['manager']];
        }

        return collect($sections)->map(function ($section) use ($user) {
            $section['items'] = collect($section['items'])->filter(function ($item) use ($user) {
                return in_array($user->role, $item['roles']);
            })->toArray();

            return $section;
        })->filter(fn ($section) => count($section['items']) > 0)->toArray();
    }
};

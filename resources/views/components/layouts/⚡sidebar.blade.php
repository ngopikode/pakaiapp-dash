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

        $this->redirect('/', true);
    }

    public function getMenuSectionsProperty(): array
    {
        $user = Auth::user();
        $storeType = StoreSetting::first()?->store_type ?? 'retail';

        $sections = [
            [
                'title' => 'Menu Utama',
                'items' => [
                    ['route' => 'dashboard',  'icon' => 'bi bi-grid-fill',        'label' => 'Dashboard',   'roles' => ['manager']],
                    ['route' => 'wallet',     'icon' => 'bi bi-wallet2',           'label' => 'Dompet',      'roles' => ['manager']],
                    ['route' => 'product',    'icon' => 'bi bi-journal-richtext',  'label' => 'Produk',      'roles' => ['manager']],
                    ['route' => 'order',      'icon' => 'bi bi-receipt-cutoff',    'label' => 'Pesanan',     'roles' => ['manager', 'cashier']],
                    ['route' => 'cashier',    'icon' => 'bi bi-cash-coin',         'label' => 'Kasir',       'roles' => ['manager', 'cashier']],
                ]
            ],
            [
                'title' => 'Pengaturan',
                'items' => [
                    ['route' => 'store-setting', 'icon' => 'bi bi-shop', 'label' => 'Pengaturan Toko', 'roles' => ['manager']],
                    ['route' => 'product-slot.buy', 'icon' => 'bi bi-cart', 'label' => 'Beli Slot Produk', 'roles' => ['manager']],
                    ['route' => 'user', 'icon' => 'bi bi-people', 'label' => 'Pengguna', 'roles' => ['manager']],
                    ['route' => 'profile', 'icon' => 'bi bi-person-gear', 'label' => 'Profil Akun', 'roles' => ['manager', 'cashier']],
                ]
            ]
        ];

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

    <div class="sidebar-heading px-4 py-4 border-bottom" style="border-color: var(--bs-border-color) !important;">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-avatar d-flex align-items-center justify-content-center text-white rounded-3 shadow-sm"
                 style="width: 40px; height: 40px; background: linear-gradient(135deg, var(--brand-caramel, #B67332), var(--brand-espresso, #321E14));">
                <i class="bi bi-cup-hot-fill fs-5"></i>
            </div>
            <div class="d-flex flex-column">
                <span class="fw-bolder fs-5 text-body text-truncate"
                      style="font-family: var(--font-serif), sans-serif; letter-spacing: -0.5px; line-height: 1.2; max-width: 170px;">
                    {{ StoreSetting::value('navbar_brand_text') }}
                </span>
                <span class="small fw-bold text-secondary text-uppercase"
                      style="font-size: 0.62rem; letter-spacing: 1.5px; opacity: 0.8;">
                    DASHBOARD TOKO
                </span>
            </div>
        </div>
    </div>

    <nav class="list-group list-group-flush my-3 flex-grow-1">
        @foreach($this->menuSections as $section)
            <div class="small text-secondary fw-bold px-4 mb-2 mt-3 text-uppercase" style="font-size: 0.65rem; letter-spacing: 0.8px; color: var(--brand-caramel, #B67332) !important;">
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
        <button type="button" wire:click="logout"
                class="btn btn-outline-danger w-100 border-0 d-flex align-items-center justify-content-center gap-2 py-2.5 rounded-3 fw-bold transition-all"
                style="background-color: rgba(220, 53, 69, 0.04); border: 1px solid rgba(220, 53, 69, 0.1) !important; color: #dc3545; font-size: 0.88rem;">
            <i class="bi bi-box-arrow-left"></i> Log Out
        </button>
    </div>
</aside>

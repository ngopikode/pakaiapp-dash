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
        $cashierRoute = $storeType === 'resto' ? 'cashier.resto' : 'cashier.retail';

        $sections = [
            [
                'title' => 'Menu Utama',
                'items' => [
                    ['route' => 'dashboard', 'icon' => 'bi bi-grid-fill', 'label' => 'Dashboard', 'roles' => ['manager']],
                    ['route' => 'product', 'icon' => 'bi bi-journal-richtext', 'label' => 'Produk', 'roles' => ['manager']],
                    ['route' => 'order', 'icon' => 'bi bi-receipt-cutoff', 'label' => 'Pesanan', 'roles' => ['manager', 'cashier']],
                    ['route' => $cashierRoute, 'icon' => 'bi bi-cash-coin', 'label' => 'Kasir', 'roles' => ['manager', 'cashier']],
                ]
            ],
            [
                'title' => 'Pengaturan',
                'items' => [
                    ['route' => 'store-setting', 'icon' => 'bi bi-shop', 'label' => 'Pengaturan Toko', 'roles' => ['manager']],
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

    <div class="sidebar-heading text-center py-4">
        <div class="d-flex flex-column align-items-center">
            <span class="font-script text-brand"
                  style="font-size: 2.2rem; line-height: 1;">{{ StoreSetting::value('navbar_brand_text') }}</span>
            <small class="text-muted fw-bold" style="font-size: 0.65rem; letter-spacing: 2px;">
                DASHBOARD</small>
        </div>
    </div>

    <nav class="list-group list-group-flush my-2 flex-grow-1">
        @foreach($this->menuSections as $section)
            <div class="small text-muted fw-bold px-4 mb-2 mt-2 text-uppercase" style="font-size: 0.7rem;">
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

    <div class="p-3 border-top">
        <button type="button" wire:click="logout"
                class="btn btn-outline-danger w-100 border-0 d-flex align-items-center justify-content-center gap-2 py-2">
            <i class="bi bi-box-arrow-left"></i> Log Out
        </button>
    </div>
</aside>

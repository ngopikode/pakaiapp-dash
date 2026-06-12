<?php

use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Title("Pengaturan")]
class extends Component {
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
                'title' => 'Katalog & Inventaris',
                'items' => [
                    ['route' => 'product', 'icon' => 'bi bi-journal-richtext', 'label' => 'Katalog Produk', 'roles' => ['manager']],
                    ['route' => 'product-slot.buy', 'icon' => 'bi bi-cart-plus', 'label' => 'Beli Slot Produk', 'roles' => ['manager'], 'badge' => 'Baru'],
                ]
            ],
            [
                'title' => 'Sistem & Pengaturan',
                'items' => [
                    ['route' => 'wallet', 'icon' => 'bi bi-wallet2', 'label' => 'Dompet & Saldo', 'roles' => ['manager']],
                    ['route' => 'store-setting', 'icon' => 'bi bi-shop', 'label' => 'Pengaturan Toko', 'roles' => ['manager']],
                    ['route' => 'user', 'icon' => 'bi bi-people', 'label' => 'Manajemen Pengguna', 'roles' => ['manager']],
                    ['route' => 'profile', 'icon' => 'bi bi-person-gear', 'label' => 'Profil Akun', 'roles' => ['manager', 'cashier']],
                ]
            ]
        ];

        if ($storeType === 'resto') {
            $sections[0]['items'][] = ['route' => 'raw-material', 'icon' => 'bi bi-box-seam', 'label' => 'Bahan Baku & Resep', 'roles' => ['manager']];
        }

        return collect($sections)->map(function ($section) use ($user) {
            $section['items'] = collect($section['items'])->filter(function ($item) use ($user) {
                return in_array($user->role, $item['roles']);
            })->toArray();

            return $section;
        })->filter(fn($section) => count($section['items']) > 0)->toArray();
    }
};
?>

<div>
    <!-- Custom Header for Mobile Menu -->
    <div class="bg-body border-bottom px-3 py-3 d-flex align-items-center justify-content-between sticky-top z-3">
        <h1 class="h4 mb-0 fw-bold">Menu Lainnya</h1>
        <button type="button" x-data="themeToggle" @click="toggleTheme()" class="btn btn-light rounded-circle shadow-sm"
                style="width: 40px; height: 40px;">
            <i x-show="theme === 'dark'" class="bi bi-sun-fill text-warning"></i>
            <i x-show="theme === 'light'" class="bi bi-moon-stars"></i>
        </button>
    </div>

    <div class="container-fluid px-3 py-4" style="padding-bottom: 100px !important;">

        <!-- User Info Card -->
        <div class="card border-0 shadow-sm rounded-4 mb-4"
             style="background: linear-gradient(135deg, var(--brand-caramel, #B67332), var(--brand-espresso, #321E14));">
            <div class="card-body p-3 d-flex align-items-center gap-3">
                <div
                    class="bg-white text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm"
                    style="width: 50px; height: 50px; font-size: 1.2rem;">
                    {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                </div>
                <div class="text-white min-w-0">
                    <h5 class="mb-0 fw-bold text-truncate">{{ Auth::user()->name }}</h5>
                    <div class="small opacity-75 text-truncate">
                        {{ Auth::user()->email }} &bull; {{ ucfirst(Auth::user()->role) }}</div>
                </div>
            </div>
        </div>

        @foreach($this->menuSections as $section)
            <div class="mb-4">
                <h6 class="text-secondary fw-bold ms-2 mb-2 text-uppercase"
                    style="font-size: 0.75rem; letter-spacing: 1px;">{{ $section['title'] }}</h6>
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="list-group list-group-flush">
                        @foreach($section['items'] as $item)
                            <a href="{{ route($item['route']) }}" wire:navigate.hover
                               class="list-group-item list-group-item-action d-flex align-items-center justify-content-between py-3 px-3 border-bottom border-light">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        class="bg-body-tertiary rounded-3 d-flex align-items-center justify-content-center text-primary"
                                        style="width: 36px; height: 36px;">
                                        <i class="{{ $item['icon'] }} fs-5"></i>
                                    </div>
                                    <span class="fw-semibold text-body">{{ $item['label'] }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    @if(isset($item['badge']))
                                        <span class="badge bg-danger rounded-pill">{{ $item['badge'] }}</span>
                                    @endif
                                    <i class="bi bi-chevron-right text-secondary opacity-50"></i>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Action Card -->
        <div class="card border-0 shadow-sm rounded-4 mt-4">
            <div class="list-group list-group-flush">
                <style>
                    @media (display-mode: standalone) {
                        .pwa-install-item {
                            display: none !important;
                        }
                    }
                </style>
                <button type="button" onclick="if(window.installPwa) window.installPwa()"
                        class="pwa-install-item list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 px-3 text-success fw-bold">
                    <div class="bg-success bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 36px; height: 36px;">
                        <i class="bi bi-download"></i>
                    </div>
                    Install App ke Layar Utama
                </button>

                <button type="button" wire:click="logout"
                        class="list-group-item list-group-item-action d-flex align-items-center gap-3 py-3 px-3 text-danger fw-bold border-top-0">
                    <div class="bg-danger bg-opacity-10 rounded-3 d-flex align-items-center justify-content-center"
                         style="width: 36px; height: 36px;">
                        <i class="bi bi-box-arrow-left"></i>
                    </div>
                    Log Out
                </button>
            </div>
        </div>

    </div>
</div>

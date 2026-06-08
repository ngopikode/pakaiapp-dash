<?php

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use App\Models\Order;
use Livewire\Attributes\Computed;

new class extends Component {
    public function getNavItemsProperty(): array
    {
        $user = Auth::user();

        $items = [
            ['route' => 'dashboard', 'icon' => 'bi bi-grid-fill', 'label' => 'Home', 'roles' => ['manager']],
            ['route' => 'order', 'icon' => 'bi bi-receipt-cutoff', 'label' => 'Pesanan', 'roles' => ['manager', 'cashier']],
            ['route' => 'cashier', 'icon' => 'bi bi-cash-coin', 'label' => 'Kasir', 'roles' => ['manager', 'cashier']],
            ['route' => 'profile', 'icon' => 'bi bi-person-fill', 'label' => 'Profil', 'roles' => ['manager', 'cashier']],
        ];

        return collect($items)->filter(function ($item) use ($user) {
            return in_array($user->role, $item['roles']);
        })->toArray();
    }

    #[Computed]
    public function pendingOrdersCount(): int
    {
        try {
            return Order::where('status', 'pending')->count();
        } catch (\Exception $e) {
            return 0;
        }
    }
};
?>

<div class="fixed-bottom bottom-navbar-wrapper d-flex justify-content-center w-100" 
     style="z-index: 1040;" wire:poll.15s.visible>
     
    <div class="nav-container shadow-lg p-0" style="overflow: hidden;">
        <div class="d-flex justify-content-around align-items-center" style="height: 60px; padding: 0 0.25rem;">
            @foreach($this->navItems as $item)
                @php
                    $isActive = request()->routeIs($item['route'] . '*'); // use wildcard to match children
                    if($item['route'] == 'dashboard' && !request()->routeIs('dashboard')) $isActive = false;
                @endphp
                <a href="{{ route($item['route']) }}" wire:navigate onclick="window.showLoader()"
                   class="text-decoration-none d-flex flex-column align-items-center justify-content-center w-100 h-100 position-relative p-0 {{ $isActive ? 'text-primary' : 'text-secondary' }}">
                    <div class="position-relative d-flex flex-column align-items-center justify-content-center mt-1">
                        <i class="{{ $item['icon'] }} fs-5 mb-0" style="{{ $isActive ? '' : 'opacity: 0.7;' }}"></i>
                        @if($item['route'] === 'order' && $this->pendingOrdersCount > 0)
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge border border-white rounded-circle bg-danger badge-pulse d-flex align-items-center justify-content-center"
                                style="font-size: 0.5rem; width: 16px; height: 16px;">
                                {{ $this->pendingOrdersCount > 9 ? '9+' : $this->pendingOrdersCount }}
                            </span>
                        @endif
                    </div>
                    <span class="small fw-bold mt-1" style="font-size: 0.6rem; {{ $isActive ? '' : 'opacity: 0.8;' }}">{{ $item['label'] }}</span>
                    @if($isActive)
                        <div class="position-absolute bottom-0 start-50 translate-middle-x bg-primary active-indicator"></div>
                    @endif
                </a>
            @endforeach

            <!-- Lainnya Link -->
            @php
                $isMenuActive = request()->routeIs('menu');
            @endphp
            <a href="{{ route('menu') }}" wire:navigate onclick="window.showLoader()"
               class="text-decoration-none d-flex flex-column align-items-center justify-content-center w-100 h-100 position-relative p-0 {{ $isMenuActive ? 'text-primary' : 'text-secondary' }}">
                <div class="position-relative d-flex flex-column align-items-center justify-content-center mt-1">
                    <i class="bi bi-list fs-5 mb-0" style="{{ $isMenuActive ? '' : 'opacity: 0.7;' }}"></i>
                </div>
                <span class="small fw-bold mt-1" style="font-size: 0.6rem; {{ $isMenuActive ? '' : 'opacity: 0.8;' }}">Lainnya</span>
                @if($isMenuActive)
                    <div class="position-absolute bottom-0 start-50 translate-middle-x bg-primary active-indicator"></div>
                @endif
            </a>
        </div>
    </div>
</div>

<style>
    /* Default (Mobile Phone) - Solid Edge-to-Edge */
    .bottom-navbar-wrapper {
        bottom: 0;
    }
    .nav-container {
        background-color: var(--bs-body-bg);
        border-top: 1px solid var(--bs-border-color-translucent);
        width: 100%;
        border-radius: 0;
        padding-bottom: env(safe-area-inset-bottom, 0);
    }
    .active-indicator {
        width: 24px; 
        height: 3px; 
        border-radius: 3px 3px 0 0;
    }
    :root {
        --bottom-nav-height: 60px;
    }

    /* Tablet (iPad) - Floating Glass Dock */
    @media (min-width: 768px) {
        .bottom-navbar-wrapper {
            bottom: calc(12px + env(safe-area-inset-bottom, 0px));
        }
        .nav-container {
            background: rgba(var(--bs-body-bg-rgb, 255, 255, 255), 0.65) !important;
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(var(--bs-body-color-rgb, 0, 0, 0), 0.08);
            border-top: none;
            border-radius: 2rem !important;
            width: 90%;
            max-width: 400px;
            padding-bottom: 0;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08) !important;
        }
        .active-indicator {
            width: 5px; 
            height: 5px; 
            border-radius: 50%;
            margin-bottom: 4px;
        }
        :root {
            --bottom-nav-height: 72px; /* 60px height + 12px bottom */
        }
    }

    /* Dark mode override for glass dock on tablet */
    @media (min-width: 768px) {
        [data-bs-theme="dark"] .nav-container {
            background: rgba(var(--bs-body-bg-rgb, 30, 30, 30), 0.45) !important;
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3) !important;
        }
    }

    #page-content-wrapper {
        padding-bottom: calc(var(--bottom-nav-height) + env(safe-area-inset-bottom, 20px)) !important;
    }
</style>

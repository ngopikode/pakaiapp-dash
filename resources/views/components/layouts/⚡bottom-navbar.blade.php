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
            ['route' => 'dashboard',  'icon' => 'bi bi-grid-fill',        'label' => 'Home',      'roles' => ['manager']],
            ['route' => 'order',      'icon' => 'bi bi-receipt-cutoff',   'label' => 'Pesanan',   'roles' => ['manager', 'cashier']],
            ['route' => 'cashier',    'icon' => 'bi bi-cash-coin',        'label' => 'Kasir',     'roles' => ['manager', 'cashier']],
            ['route' => 'profile',    'icon' => 'bi bi-person-fill',      'label' => 'Profil',    'roles' => ['manager', 'cashier']],
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

<div class="fixed-bottom d-md-none bg-white border-top shadow-sm pb-safe bottom-navbar-mobile" style="z-index: 1040;" wire:poll.15s.visible>
    <div class="d-flex justify-content-around align-items-center" style="height: 65px;">
        @foreach($this->navItems as $item)
            @php
                $isActive = request()->routeIs($item['route'] . '*'); // use wildcard to match children
                // special case for exact match if needed, but routeIs usually works
                if($item['route'] == 'dashboard' && !request()->routeIs('dashboard')) $isActive = false;
            @endphp
            <a href="{{ route($item['route']) }}" wire:navigate onclick="window.showLoader()" class="text-decoration-none d-flex flex-column align-items-center justify-content-center w-100 h-100 position-relative {{ $isActive ? 'text-primary' : 'text-secondary' }}">
                <div class="position-relative">
                    <i class="{{ $item['icon'] }} fs-4 mb-1" style="{{ $isActive ? '' : 'opacity: 0.7;' }}"></i>
                    @if($item['route'] === 'order' && $this->pendingOrdersCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge border border-white rounded-circle bg-danger badge-pulse d-flex align-items-center justify-content-center" style="font-size: 0.55rem; width: 18px; height: 18px;">
                            {{ $this->pendingOrdersCount > 9 ? '9+' : $this->pendingOrdersCount }}
                        </span>
                    @endif
                </div>
                <span class="small fw-bold" style="font-size: 0.65rem;">{{ $item['label'] }}</span>
                @if($isActive)
                    <div class="position-absolute top-0 start-50 translate-middle-x bg-primary rounded-bottom" style="width: 30px; height: 3px;"></div>
                @endif
            </a>
        @endforeach

        <!-- Lainnya Toggle -->
        <button class="btn border-0 text-decoration-none d-flex flex-column align-items-center justify-content-center w-100 h-100 position-relative text-secondary p-0" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar" style="box-shadow: none;">
            <i class="bi bi-list fs-4 mb-1" style="opacity: 0.7;"></i>
            <span class="small fw-bold" style="font-size: 0.65rem;">Lainnya</span>
        </button>
    </div>
</div>

<style>
    .pb-safe {
        padding-bottom: env(safe-area-inset-bottom, 0);
    }
    
    :root {
        --bottom-nav-height: 0px;
    }

    @media (max-width: 767.98px) {
        :root {
            --bottom-nav-height: 65px;
        }
        body {
            /* Add padding so content doesn't get hidden behind the bottom navbar on mobile */
            padding-bottom: calc(var(--bottom-nav-height) + env(safe-area-inset-bottom, 0px)) !important;
        }
    }
</style>

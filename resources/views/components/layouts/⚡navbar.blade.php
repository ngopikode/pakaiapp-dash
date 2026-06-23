<?php

use Livewire\Component;
use App\Models\Order;
use Livewire\Attributes\Computed;

new class extends Component {
    public ?string $header = 'Dashboard Overview';

    public function logout(): void
    {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('dashboard');
    }

    #[Computed]
    public function pendingOrdersCount(): int
    {
        try {
            return Order::where('status', 'pending')->count();
        } catch (\Exception $e) {
            return 0; // Fallback for when tenant DB is not fully resolved yet during global requests
        }
    }
};
?>

<nav class="navbar navbar-expand navbar-light sticky-top px-3 px-lg-4 border-bottom shadow-sm navbar-dashboard"
     id="mainNavbar" style="min-height: 70px;">

    <div class="d-flex align-items-center justify-content-between w-100 flex-nowrap">

        <div class="d-flex align-items-center gap-2 gap-lg-3">
            <!-- Mobile/Tablet Toggle (Offcanvas) -->
            <button class="btn text-primary border-0 p-2 d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                <i class="bi bi-list fs-4"></i>
            </button>
            <span class="fs-6 fw-bold font-serif text-truncate d-lg-none" style="max-width: 130px;" title="{{ $header ?? 'Dashboard' }}">
                {{ $header ?? 'Dashboard' }}
            </span>

            <!-- Desktop Toggle -->
            <button class="btn text-primary border-0 p-2 d-none d-lg-block" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>
            <h5 class="m-0 font-serif fw-bold text-truncate d-none d-lg-block" style="max-width: 300px;">
                {{ $header ?? 'Dashboard' }}
            </h5>
        </div>

        <ul class="navbar-nav ms-auto flex-row align-items-center gap-2 gap-lg-3">

            <li class="nav-item">
                <button
                    x-data="{ theme: localStorage.getItem('theme') || 'light' }" @click="theme = theme === 'dark' ? 'light' : 'dark'; localStorage.setItem('theme', theme); document.documentElement.setAttribute('data-bs-theme', theme)"
                    class="btn btn-link nav-link text-secondary p-2"
                    title="Ganti Tema"
                >
                    <i x-show="theme === 'dark'" class="ph-fill ph-sun fs-5 text-warning" x-cloak></i>

                    <i x-show="theme === 'light'" class="ph-fill ph-moon fs-5" x-cloak></i>
                </button>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link text-secondary position-relative p-2 hover-lift d-flex align-items-center" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" wire:poll.15s.visible>
                    <i class="bi bi-bell fs-5" id="notifBellIcon"></i>
                    @if($this->pendingOrdersCount > 0)
                        <span
                            class="position-absolute top-0 start-100 translate-middle badge border border-light rounded-pill bg-danger p-1 px-2 mt-2 me-2 badge-pulse"
                            style="font-size: 0.65rem;">
                            {{ $this->pendingOrdersCount }}
                            <span class="visually-hidden">pesanan baru</span>
                        </span>
                    @endif
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2 dropdown-menu-responsive-notif" style="width: 320px;" aria-labelledby="notifDropdown">
                    <li class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center rounded-top-4">
                        <span class="fw-bold text-body">Notifikasi</span>
                        @if($this->pendingOrdersCount > 0)
                            <span class="badge bg-danger rounded-pill">{{ $this->pendingOrdersCount }} Baru</span>
                        @endif
                    </li>

                    @if($this->pendingOrdersCount > 0)
                        <li>
                            <a class="dropdown-item py-3 px-3 d-flex flex-column hover-bg-light text-wrap" href="{{ route('order') }}" wire:navigate.hover>
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <span class="fw-bold text-body fs-6"><i class="bi bi-bag-check-fill text-primary me-2"></i>Pesanan Baru</span>
                                    <small class="text-primary fw-bold"><i class="bi bi-arrow-right"></i></small>
                                </div>
                                <span class="text-muted small">Ada {{ $this->pendingOrdersCount }} pesanan yang menunggu konfirmasi pembayaran. Segera cek!</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <div class="px-3 py-4 text-center text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary opacity-50"></i>
                                <span class="small fw-bold text-body">Belum ada notifikasi baru</span>
                            </div>
                        </li>
                    @endif

                    <li class="border-top">
                        <a href="{{ route('order') }}" wire:navigate.hover class="dropdown-item text-center py-2 text-primary fw-bold small rounded-bottom-4">Lihat Semua Pesanan</a>
                    </li>
                </ul>
            </li>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center gap-2 p-1" href="#" id="navbarDropdown"
                   role="button" data-bs-toggle="dropdown" aria-expanded="false">

                    <div
                        class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center shadow-sm flex-shrink-0"
                        style="width: 38px; height: 38px;">
                        <span class="fw-bold">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</span>
                    </div>

                    <div class="text-start lh-1 d-none d-lg-block">
                        <div class="fw-bold small text-dark text-truncate" style="max-width: 120px;">
                            {{ Auth::user()->name ?? 'User' }}
                        </div>
                        <small class="text-muted" style="font-size: 0.7rem;">Admin</small>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 mt-2"
                    aria-labelledby="navbarDropdown">
                    <li class="px-3 py-2 border-bottom mb-2 d-lg-none">
                        <span class="fw-bold d-block text-dark">{{ Auth::user()->name ?? 'User' }}</span>
                        <small class="text-muted">Admin</small>
                    </li>

                    <li>
                        <a class="dropdown-item py-2" href="{{ route('profile') }}" wire:navigate.hover><i
                                class="bi bi-person me-2"></i>
                            Edit Profil
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <button type="button" wire:click="logout()"
                                class="dropdown-item py-2 text-danger">
                            <i class="bi bi-box-arrow-right me-2"></i> Log Out
                        </button>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<style>
    /* Hover animation for Bell Icon */
    .hover-lift:hover #notifBellIcon,
    .nav-link:hover #notifBellIcon {
        animation: bellRing 0.65s ease-in-out;
        color: var(--brand-caramel, #B67332) !important;
    }

    @keyframes bellRing {
        0%, 100% { transform: rotate(0); }
        15% { transform: rotate(10deg); }
        30% { transform: rotate(-10deg); }
        45% { transform: rotate(5deg); }
        60% { transform: rotate(-5deg); }
        75% { transform: rotate(2deg); }
        90% { transform: rotate(-2deg); }
    }

    /* Pulse animation for active notification badge */
    .badge-pulse {
        animation: badgePulse 2s infinite cubic-bezier(0.25, 0, 0, 1);
    }

    @keyframes badgePulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            transform: translate(-50%, -50%) scale(1);
        }
        50% {
            transform: translate(-50%, -50%) scale(1.12);
        }
        100% {
            box-shadow: 0 0 0 8px rgba(220, 53, 69, 0);
            transform: translate(-50%, -50%) scale(1);
        }
    }

    /* Responsive layout for Notification Dropdown on Mobile */
    @media (max-width: 576px) {
        .dropdown-menu-responsive-notif {
            position: fixed !important;
            top: 75px !important;
            left: 12px !important;
            right: 12px !important;
            width: calc(100% - 24px) !important;
            max-width: none !important;
            transform: none !important;
            border-radius: 1.25rem !important;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15) !important;
            border: 1px solid var(--bs-border-color) !important;
            background: rgba(var(--bs-card-bg-rgb, 255, 255, 255), 0.96) !important;
            backdrop-filter: blur(20px) !important;
            -webkit-backdrop-filter: blur(20px) !important;
        }

        .dropdown-menu-responsive-notif::before {
            display: none !important; /* Hilangkan panah default bootstrap dropdown di mobile */
        }
    }

    /* Glassmorphism for notifications container on all screen sizes */
    .dropdown-menu-responsive-notif {
        background: rgba(var(--bs-card-bg-rgb, 255, 255, 255), 0.96) !important;
        backdrop-filter: blur(20px) !important;
        -webkit-backdrop-filter: blur(20px) !important;
        border: 1px solid var(--bs-border-color) !important;
    }

    /* Micro-animation transitions */
    .dropdown-menu-responsive-notif .dropdown-item {
        transition: background-color 0.2s ease, transform 0.25s cubic-bezier(0.16, 1, 0.3, 1), color 0.2s ease;
    }

    .dropdown-menu-responsive-notif .dropdown-item:hover {
        transform: translateX(6px);
        background-color: var(--bs-tertiary-bg) !important;
    }
    
    #notifBellIcon {
        transition: color 0.3s ease, transform 0.3s ease;
    }
</style>

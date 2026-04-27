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
        return [
            [
                'title' => 'Menu Utama',
                'items' => [
                    ['route' => 'dashboard', 'icon' => 'bi bi-grid-fill', 'label' => 'Dashboard'],
                    ['route' => 'product', 'icon' => 'bi bi-journal-richtext', 'label' => 'Produk'],
                    ['route' => 'dashboard', 'icon' => 'bi bi-receipt-cutoff', 'label' => 'Pesanan'],
                    ['route' => 'cashier', 'icon' => 'bi bi-cash-coin', 'label' => 'Kasir'],
                ]
            ],
            [
                'title' => 'Pengaturan',
                'items' => [
                    ['route' => 'home', 'icon' => 'bi bi-shop', 'label' => 'Pengaturan Toko'],
                    ['route' => 'profile', 'icon' => 'bi bi-person-gear', 'label' => 'Profil Akun'],
                ]
            ]
        ];
    }
};
?>

<aside id="{{ $elementId }}">

    <div class="sidebar-heading text-center py-4">
        <div class="d-flex flex-column align-items-center">
            <span class="font-script text-brand"
                  style="font-size: 2.2rem; line-height: 1;">{{ StoreSetting::value('name') }}</span>
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

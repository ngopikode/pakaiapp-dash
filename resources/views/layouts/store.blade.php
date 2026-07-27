@php
    use App\Tenant\Models\Core\StoreSetting;
    use Illuminate\Support\Facades\Storage;

    $setting = StoreSetting::cached();

    if ($setting?->is_active) {
        $waNumber = preg_replace('/\D/', '', $setting->whatsapp_number ?: '6281234567890');
        if (str_starts_with($waNumber, '0')) $waNumber = '62' . substr($waNumber, 1);
        $storeName = $setting->name ?: 'EzMenu';
        $storeType = $setting->store_type ?: 'resto';

        $orderTypes = [];
        if ($storeType === 'resto') {
            if ($setting->is_dinein_active)   $orderTypes[] = ['id' => 'dinein',   'label' => 'Makan Sini'];
            if ($setting->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Bungkus'];
        } else {
            if ($setting->is_takeaway_active) $orderTypes[] = ['id' => 'takeaway', 'label' => 'Ambil Sendiri'];
        }
        if ($setting->is_delivery_active) $orderTypes[] = ['id' => 'delivery', 'label' => 'Diantar'];
        if (empty($orderTypes))           $orderTypes[] = ['id' => 'takeaway', 'label' => 'Takeaway'];

        $isOpenNow  = $setting->isOpenNow();
        $todayHours = $setting->getTodayHours();
    }
@endphp

@if (! $setting?->is_active)
@include('layouts._partials.store-not-found')
@else
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-no-progress-bar>

@include('layouts._partials.store-head', [
    'setting' => $setting,
    'storeName' => $storeName
])

<body>

{{-- ===== PAGE LOADER — OUTSIDE x-data so Alpine errors can NEVER block it ===== --}}
@include('pages.tenant.store.resto.partials._loader')

<div
    class="bg-[var(--background)] min-h-screen text-[var(--foreground)] pb-28 font-sans antialiased relative selection:bg-[var(--primary)] selection:text-black"
    x-data="storeApp"
    data-store-closed="{{ $isOpenNow ? 0 : 1 }}"
    data-default-order-type="{{ $orderTypes[0]['id'] }}"
    data-wa-number="{{ $waNumber }}"
    data-duitku-enabled="{{ config('duitku.enabled') ? 1 : 0 }}"
    data-midtrans-enabled="{{ config('midtrans.server_key') ? 1 : 0 }}"
    data-tax-active="{{ $setting->is_tax_active ? 1 : 0 }}"
    data-tax-rate="{{ $setting->tax_rate ?? 10.00 }}"
    data-service-active="{{ $setting->is_service_charge_active ? 1 : 0 }}"
    data-service-rate="{{ $setting->service_charge_rate ?? 5.00 }}"
    @open-qr-modal.window="qrOpen = true"
    @show-toast.window="showToast($event.detail.message)"
    @open-options-modal.window="openOption($event.detail.product)"
    @open-checkout-modal.window="openCheckout()"
    @keydown.escape.window="
        if (historyOpen)     { historyOpen = false; }
        else if (checkoutOpen)    { closeCheckout(); }
        else if (optionOpen) { closeOption(); }
        else if (qrOpen)     { qrOpen = false; }
    "
    @touchstart="handleTouchStart($event)"
    @touchmove="handleTouchMove($event)"
    @touchend="handleTouchEnd()"
>
    {{-- ===== PULL TO REFRESH INDICATOR (GLOBAL) ===== --}}
    <div
        class="max-w-xl mx-auto flex justify-center items-end overflow-hidden transition-all duration-200 ease-out"
        :style="`height: ${isRefreshing ? 60 : Math.min(pullY, 60)}px; opacity: ${isRefreshing ? 1 : Math.min(pullY / 60, 1)}`"
    >
        <div class="flex items-center gap-2 text-[var(--text-secondary)] pb-3">
            <template x-if="isRefreshing">
                <div
                    class="w-5 h-5 border-2 border-[var(--primary-color)] border-t-transparent rounded-full animate-spin"></div>
            </template>
            <template x-if="!isRefreshing">
                <div class="w-5 h-5 flex items-center justify-center transition-transform"
                     :style="`transform: rotate(${pullY * 3}deg)`">↓
                </div>
            </template>
            <span class="text-xs font-bold"
                  x-text="isRefreshing ? 'Memuat ulang menu...' : 'Tarik untuk refresh'"></span>
        </div>
    </div>

    {{-- ===== BANNER KUNING TOKO TUTUP JIKA ADA OPERATING HOURS & TUTUP --}}
    @if(isset($todayHours) && !empty($todayHours))
        @include('layouts._partials._closed-banner', ['isOpenNow' => $isOpenNow, 'todayHours' => $todayHours])
    @endif

    {{-- ===== HERO (Pure Blade — zero extra DB query) ===== --}}
    @include('pages.tenant.store.resto.partials._hero', ['setting' => $setting, 'isOpenNow' => $isOpenNow ?? false, 'todayHours' => $todayHours ?? []])

    {{-- ===== PAGE CONTENT (product-list is the only Livewire component) ===== --}}
    {{ $slot }}

    {{-- ===== GLOBAL MODALS & TOAST ===== --}}
    @include('layouts._partials.store-globals', ['setting' => $setting])

    {{-- ===== OPTION MODAL (100% Client-Side) ===== --}}
    @include('pages.tenant.store.resto.modals.option-modal')

    {{-- ===== CHECKOUT MODAL (100% Client-Side) ===== --}}
    @include('pages.tenant.store.resto.modals.checkout-modal', ['orderTypes' => $orderTypes])

    {{-- ===== RIWAYAT MODAL (100% Client-Side) ===== --}}
    @include('pages.tenant.store.resto.modals.history-modal')

    {{-- ===== AI FLOATING CHAT ===== --}}
    <livewire:components::tenant.ai-floating-chat/>
</div>

@livewireScripts
</body>
</html>
@endif

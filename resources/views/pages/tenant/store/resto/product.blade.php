<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-no-progress-bar>
@include('pages.tenant.store.resto.partials._product-head')
<body>

{{-- ===== PAGE LOADER — OUTSIDE x-data so Alpine errors can NEVER block it ===== --}}
@include('pages.tenant.store.resto.partials._loader')

<div
    class="bg-[var(--bg-soft)] min-h-screen text-[var(--foreground)] font-sans antialiased relative selection:bg-[var(--primary-color)] selection:text-black"
    x-data="storeApp"
    data-default-order-type="{{ $orderTypes[0]['id'] }}"
    data-wa-number="{{ $waNumber }}"
    data-duitku-enabled="{{ config('duitku.enabled') ? 1 : 0 }}"
    data-midtrans-enabled="{{ config('midtrans.server_key') ? 1 : 0 }}"
    data-tax-active="{{ $setting?->is_tax_active ? 1 : 0 }}"
    data-tax-rate="{{ $setting?->tax_rate ?? 10.00 }}"
    data-service-active="{{ $setting?->is_service_charge_active ? 1 : 0 }}"
    data-service-rate="{{ $setting?->service_charge_rate ?? 5.00 }}"
    data-is-app-fee-active="{{ ($setting?->is_application_fee_passed ?? false) ? 1 : 0 }}"
    data-app-fee-amount="{{ $appFeeAmount }}"
    @show-toast.window="showToast($event.detail.message)"
    @open-options-modal.window="openOption($event.detail.product)"
    @open-checkout-modal.window="openCheckout()"
    @keydown.escape.window="
        if (checkoutOpen)    { closeCheckout(); }
        else if (optionOpen) { closeOption(); }
    "
>
    <div
        x-data="{
            product: @js($productData),
            scrolled: false,
            pullY: 0,
            isRefreshing: false,
            startY: 0,
            get qtyInCart() {
                const i = cart.find(x => x.cartName === this.product.name);
                return i ? i.qty : 0;
            }
        }"
        @scroll.window="scrolled = window.scrollY > window.innerHeight * 0.25"
        @touchstart.passive="startY = $event.touches[0].clientY"
        @touchmove.passive="
            if (window.scrollY === 0 && !isRefreshing) {
                pullY = Math.max(0, $event.touches[0].clientY - startY);
            }
        "
        @touchend="
            if (pullY > 60 && !isRefreshing) {
                isRefreshing = true;
                pullY = 60;
                setTimeout(() => window.location.reload(), 800);
            } else {
                pullY = 0;
            }
        "
    >
        @include('pages.tenant.store.resto.partials._product-floating-header')
        @include('pages.tenant.store.resto.partials._pull-to-refresh')
        @include('pages.tenant.store.resto.partials._product-hero-image')
        @include('pages.tenant.store.resto.partials._product-content')
        @include('pages.tenant.store.resto.partials._product-action-bar')
    </div>{{-- /product x-data --}}

    @include('pages.tenant.store.resto.partials._toast')
    @include('pages.tenant.store.resto.modals.option-modal')
    @include('pages.tenant.store.resto.modals.checkout-modal', ['orderTypes' => $orderTypes])

</div>

@livewireScripts
</body>
</html>

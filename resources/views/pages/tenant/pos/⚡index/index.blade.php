<div>
    <!-- Render komponen sesuai dengan store_type -->
    @if($storeType === 'resto')
        <livewire:pages::tenant.pos.resto-cashier/>
    @elseif($storeType === 'retail')
        <livewire:pages::tenant.pos.retail-cashier/>
    @else
        <div class="alert alert-danger text-center m-5">
            Tipe toko tidak valid atau belum diatur!
        </div>

    @endif
</div>

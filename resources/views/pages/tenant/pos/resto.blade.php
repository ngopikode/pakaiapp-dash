<x-layouts::app title="Kasir" :navbar="['mode' => 'pos', 'title' => 'PakaiApp POS']">
    <livewire:pages::tenant.pos.resto-cashier :addToOrder="request('add_to_order')" />
</x-layouts::app>

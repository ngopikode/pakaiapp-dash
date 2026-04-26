<?php

use App\Models\Order;
use App\Models\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::print')]
class extends Component {

    public Order $order;
    public $store;

    // Menangkap parameter {code} dari URL
    public function mount($code): void
    {
        // Tarik data order beserta itemnya, kalau ga ada langsung lempar 404
        $this->order = Order::with('items')->where('invoice_code', $code)->firstOrFail();

        // Tarik info toko buat nampilin logo/nama toko di struk
        $this->store = StoreSetting::first();
    }

    // Biar tab browser & crawler Google ngebaca judul spesifik
    public function title(): string
    {
        $storeName = $this->store->name ?? 'Toko';
        return "Invoice {$this->order->invoice_code} - {$storeName}";
    }
};

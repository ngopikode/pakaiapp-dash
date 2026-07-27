<?php

use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::print')]
class extends Component
{
    public Order $order;

    public $store;

    public function mount($code): void
    {
        $this->order = Order::with('items')->where('invoice_code', $code)->firstOrFail();
        $this->store = StoreSetting::first();
    }

    public function markAsPrinted(): void
    {
        if (!$this->order->is_printed) {
            $this->order->update(['is_printed' => true]);
        }
    }

    public function title(): string
    {
        $storeName = $this->store->name ?? 'Toko';

        return "Receipt {$this->order->invoice_code} - {$storeName}";
    }
};

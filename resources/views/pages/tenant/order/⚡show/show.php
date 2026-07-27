<?php

use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\StoreSetting;
use Livewire\Attributes\Layout;
use Livewire\Component;

new #[Layout('layouts::mobile')]
class extends Component
{
    public Order $order;

    public $store;

    public function mount($code): void
    {
        $this->order = Order::with('items')->where('invoice_code', $code)->firstOrFail();
        $this->store = StoreSetting::first();
    }

    public function refreshOrder(): void
    {
        $this->order->refresh();
    }

    public function title(): string
    {
        $storeName = $this->store->name ?? 'Toko';

        return "Detail Pesanan {$this->order->invoice_code} - {$storeName}";
    }

    public function generateWaText(): string
    {
        $waLines = [
            'Halo admin, pesanan baru nih!',
            "*Invoice:* {$this->order->invoice_code}",
            '*Nama:* ' . ($this->order->customer_name ?: 'Guest'),
            '*Tipe:* ' . ucfirst($this->order->order_type),
            '*Status Pembayaran:* ' . ($this->order->status === 'paid' || $this->order->status === 'completed' ? 'Sudah Dibayar' : 'Belum Dibayar'),
        ];

        if ($this->order->notes) {
            $waLines[] = "*Catatan/Meja:* {$this->order->notes}";
        }

        $waLines[] = '';
        $waLines[] = '*Daftar Pesanan:*';

        foreach ($this->order->items as $item) {
            $waLines[] = "- {$item->quantity}x {$item->product_name}";
        }

        $waLines[] = '';
        $waLines[] = '*Subtotal:* Rp ' . number_format($this->order->subtotal, 0, ',', '.');

        if ($this->order->service_charge_amount > 0) {
            $waLines[] = '*Biaya Layanan:* Rp ' . number_format($this->order->service_charge_amount, 0, ',', '.');
        }
        if ($this->order->tax_amount > 0) {
            $waLines[] = '*Pajak PB1:* Rp ' . number_format($this->order->tax_amount, 0, ',', '.');
        }

        $waLines[] = '*Total Tagihan:* Rp ' . number_format($this->order->total_price, 0, ',', '.');

        return implode("\n", $waLines);
    }

    public function getWaUrl(): string
    {
        $number = $this->store->whatsapp_number ?? '';
        if (!$number) return '';

        $text = $this->generateWaText();

        return "https://wa.me/{$number}?text=" . urlencode($text);
    }
};

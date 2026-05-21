<?php

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {

    public ?int $orderId = null;

    #[On('openModal')]
    public function openModal($orderId): void
    {
        $this->orderId = $orderId;
        $this->dispatch('show-order-modal');
        $this->dispatch('show-bootstrap-modal');
    }

    public function with(): array
    {
        return [
            'order' => Order::with('items')->find($this->orderId)
        ];
    }

    public function triggerPayment(): void
    {
        if ($this->orderId) {
            $this->dispatch('hide-order-modal');
            $this->dispatch('trigger-payment-modal', orderId: $this->orderId);
        }
    }

    public function updateStatus($newStatus, $cancellationNote = null): void
    {
        if (!$this->orderId) return;

        $order = Order::with('items')->find($this->orderId);
        if (!$order) return;

        // Hanya pesanan pending yang boleh di-cancel
        if ($newStatus === 'cancelled' && $order->status !== 'pending') {
            $this->dispatch('notify', message: 'Pesanan sudah ' . ($order->status === 'paid' ? 'lunas' : 'dibatalkan') . ', tidak bisa dibatalkan lagi.', type: 'error');
            $this->dispatch('hide-order-modal');
            return;
        }

        $updateData = ['status' => $newStatus];

        if ($newStatus === 'cancelled' && $cancellationNote) {
            $updateData['cancellation_note'] = $cancellationNote;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $updateData, $newStatus) {
            $order->update($updateData);

            // Kembalikan stok saat cancel
            if ($newStatus === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        \App\Models\ProductVariant::where('id', $item->variant_id)
                            ->increment('stock', $item->quantity);
                    }
                }
            }
        });

        $this->dispatch('order-updated');

        if ($newStatus === 'cancelled') {
            $this->dispatch('hide-order-modal');
            $this->dispatch('notify', message: 'Pesanan berhasil dibatalkan.', type: 'success');
        } else {
            $this->dispatch('notify', message: 'Pesanan berhasil diperbarui.', type: 'success');
        }
    }
};

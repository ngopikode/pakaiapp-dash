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

        if ($newStatus === 'cancelled' && $order->status === 'cancelled') {
            $this->dispatch('notify', message: 'Pesanan sudah dibatalkan sebelumnya.', type: 'error');
            $this->dispatch('hide-order-modal');
            return;
        }

        if ($newStatus === 'cancelled' && $order->status !== 'pending') {
            if ($order->is_printed || \Carbon\Carbon::parse($order->created_at)->toDateString() !== today()->toDateString()) {
                $this->dispatch('notify', message: 'Pesanan yang sudah dicetak struk atau lewat hari tidak bisa dibatalkan.', type: 'error');
                $this->dispatch('hide-order-modal');
                return;
            }
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
                if ($order->getOriginal('status') !== 'pending') {
                    app(\App\Services\BillingService::class)->processVoidPenalty($order);
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

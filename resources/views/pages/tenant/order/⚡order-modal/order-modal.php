<?php

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;

new class extends Component {
    public $selectedOrder = null;

    #[On('openModal')]
    public function openModal($orderId): void
    {
        $this->selectedOrder = Order::with('items')->find($orderId);
        $this->dispatch('show-order-modal');
        $this->dispatch('show-bootstrap-modal');
    }

    public function updateStatus($newStatus): void
    {
        if ($this->selectedOrder) {
            $this->selectedOrder->update(['status' => $newStatus]);

            // Refresh data di modal
            $this->selectedOrder = Order::with('items')->find($this->selectedOrder->id);

            // Kasih tau tabel di belakang buat refresh otomatis
            $this->dispatch('order-updated');
            $this->dispatch('notify', message: 'Pesanan berhasil diperbarui!');

            // Jika dibatalkan, langsung tutup modalnya
            if ($newStatus === 'cancelled') {
                $this->dispatch('hide-order-modal');
            }
        }
    }
};

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

    public function updateStatus($newStatus): void
    {
        if ($this->orderId) {

            Order::where('id', $this->orderId)->update([
                'status' => $newStatus
            ]);

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

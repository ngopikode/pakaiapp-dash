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
        if ($this->orderId) {

            $updateData = ['status' => $newStatus];

            if ($newStatus === 'cancelled' && $cancellationNote) {
                $updateData['cancellation_note'] = $cancellationNote;
            }

            Order::where('id', $this->orderId)->update($updateData);

            // Kasih tau tabel di belakang buat refresh otomatis
            $this->dispatch('order-updated');
            $this->dispatch('notify', message: 'Pesanan berhasil diperbarui!');

            // Jika dibatalkan, langsung tutup modalnya
            if ($newStatus === 'cancelled') {
                $this->dispatch('hide-order-modal');
                // Kasih tau alert success bahwa batal dengan alasan tercatat
                $this->dispatch('notify', message: 'Pesanan dibatalkan dengan catatan!');
            }
        }
    }
};

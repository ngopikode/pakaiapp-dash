<?php

use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public int $perPage = 10;

    #[On('order-updated')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->perPage = 10;
    }

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
        $this->perPage = 10;
    }

    public function loadMore(): void
    {
        $this->perPage += 10;
    }

    #[On('cancel-confirmed')]
    public function handleCancelConfirmed($orderId, $note): void
    {
        $this->updateStatus($orderId, 'cancelled', $note);
    }

    public function updateStatus($id, $status, $cancellationNote = null): void
    {
        $order = Order::with('items')->find($id);
        if (!$order) return;

        // Hanya pesanan pending yang boleh di-cancel
        if ($status === 'cancelled' && $order->status !== 'pending') {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Pesanan sudah ' . ($order->status === 'paid' ? 'lunas' : 'dibatalkan') . ', tidak bisa dibatalkan.']);
            $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
            return;
        }

        \Illuminate\Support\Facades\DB::transaction(function () use ($order, $status, $cancellationNote) {
            $updateData = ['status' => $status];
            if ($status === 'cancelled' && $cancellationNote) {
                $updateData['cancellation_note'] = $cancellationNote;
            }
            $order->update($updateData);

            // Kembalikan stok saat cancel
            if ($status === 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->variant_id) {
                        \App\Models\ProductVariant::where('id', $item->variant_id)
                            ->increment('stock', $item->quantity);
                    }
                }
            }
        });

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Status pesanan diperbarui.']);
        $this->js("window.dispatchEvent(new CustomEvent('close-cancel-modal'));");
    }


    public function with(): array
    {
        $query = Order::query()
            ->when($this->search, function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_code', 'like', '%' . $this->search . '%')
                    ->orWhere('table_number', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            });

        return [
            'orders' => $query->latest()->paginate($this->perPage),

            'allCount' => Order::count(),
            'pendingCount' => Order::where('status', 'pending')->count(),
            'paidCount' => Order::where('status', 'paid')->count(),
            'progressCount' => Order::where('status', 'progress')->count(),
            'completedCount' => Order::where('status', 'completed')->count(),
            'cancelledCount' => Order::where('status', 'cancelled')->count(),
        ];
    }
};

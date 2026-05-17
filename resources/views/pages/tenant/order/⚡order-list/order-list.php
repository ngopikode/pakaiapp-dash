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
        $this->dispatch('close-cancel-modal');
    }

    public function updateStatus($id, $status, $cancellationNote = null): void
    {
        $order = Order::find($id);
        if ($order) {
            $updateData = ['status' => $status];
            if ($status === 'cancelled' && $cancellationNote) {
                $updateData['cancellation_note'] = $cancellationNote;
            }
            $order->update($updateData);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Status pesanan diperbarui.']);
        }
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
            'cancelledCount' => Order::where('status', 'cancelled')->count(),
        ];
    }
};

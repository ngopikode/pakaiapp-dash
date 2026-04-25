<?php

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

new class extends Component {
    use WithPagination;

    public string $search = '';
    public string $statusFilter = 'all';
    public int $perPage = 10;

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

    public function updateStatus($id, $status): void
    {
        $order = Order::find($id);
        if ($order) {
            $order->status = $status;
            $order->save();
            $this->dispatch('notify', message: 'Status berhasil diperbarui!');
        }
    }

    public function with(): array
    {
        $query = Order::query()
            ->when($this->search, function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('id', 'like', '%' . $this->search . '%')
                    ->orWhere('order_code', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            });

        return [
            'orders' => $query->latest()->paginate($this->perPage),
            'allCount' => Order::count(),
            'pendingCount' => Order::where('status', 'pending')->count(),
            'confirmedCount' => Order::where('status', 'confirmed')->count(),
            'completedCount' => Order::where('status', 'completed')->count(),
        ];
    }
};

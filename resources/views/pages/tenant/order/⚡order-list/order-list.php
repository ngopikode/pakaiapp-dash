<?php

use App\Tenant\Models\Core\Order;
use App\Tenant\Models\Core\StoreSetting;
use Illuminate\Support\Facades\DB;
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

    public function with(): array
    {
        $query = Order::query()
            ->when($this->search, function ($q) {
                $q->where('customer_name', 'like', '%' . $this->search . '%')
                    ->orWhere('invoice_code', 'like', '%' . $this->search . '%')
                    ->orWhere('table_number', 'like', '%' . $this->search . '%')
                    ->orWhere('notes', 'like', '%' . $this->search . '%');
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            });

        $counts = DB::table('orders')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $allCount = $counts->sum();

        return [
            'orders' => $query->with('items')->latest()->paginate($this->perPage),

            'allCount' => $allCount,
            'pendingCount' => $counts->get('pending', 0),
            'paidCount' => $counts->get('paid', 0),
            'progressCount' => $counts->get('progress', 0),
            'completedCount' => $counts->get('completed', 0),
            'cancelledCount' => $counts->get('cancelled', 0),
            'storeType' => StoreSetting::first()?->store_type ?? 'retail',
        ];
    }
};

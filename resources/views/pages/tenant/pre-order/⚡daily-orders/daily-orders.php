<?php

use App\Tenant\Models\Core\Order;
use App\Tenant\Services\PreOrderService;
use Carbon\Carbon;
use Livewire\Attributes\Title;
use Livewire\Component;
use Throwable;

new #[Title('Pesanan Terjadwal')]
class extends Component
{
    public string $selectedDate;

    protected ?PreOrderService $preOrderService = null;

    protected function preOrderService(): PreOrderService
    {
        return $this->preOrderService ??= app(PreOrderService::class);
    }

    public function mount(): void
    {
        $this->selectedDate = Carbon::today('Asia/Jakarta')->toDateString();
    }

    public function completeAll(): void
    {
        try {
            $count = $this->preOrderService()->completeAllForDate(
                Carbon::parse($this->selectedDate, 'Asia/Jakarta')
            );
            $this->dispatch('notify', ['type' => 'success', 'message' => "{$count} pesanan berhasil diselesaikan."]);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal menyelesaikan pesanan.']);
        }
    }

    public function cancelOrder(int $orderId): void
    {
        try {
            $order = Order::whereDate('delivery_date', $this->selectedDate)->findOrFail($orderId);
            $order->update(['status' => 'cancelled']);
            $this->dispatch('notify', ['type' => 'success', 'message' => 'Pesanan dibatalkan.']);
        } catch (Throwable $e) {
            report($e);
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Gagal membatalkan pesanan.']);
        }
    }

    public function with(): array
    {
        $date = Carbon::parse($this->selectedDate, 'Asia/Jakarta');

        $orders = Order::with(['items', 'deliverySlot', 'deliveryZone'])
            ->whereDate('delivery_date', $date->toDateString())
            ->whereNotIn('status', ['cancelled'])
            ->latest()
            ->get();

        $pendingCount = $orders->where('status', 'pending')->count();
        $paidCount = $orders->where('status', 'paid')->count();

        return compact('orders', 'pendingCount', 'paidCount');
    }
};

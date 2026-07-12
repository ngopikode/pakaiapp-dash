<?php

use App\Tenant\Models\Core\Order;
use Livewire\Attributes\Title;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Component;

new #[Title("Tampilan Dapur")]
class extends Component {
    public function markAsProcessing($orderId): void
    {
        $order = Order::find($orderId);
        if ($order && $order->status !== 'cancelled') {
            // Update only waiting items
            $order->items()->where('kitchen_status', 'waiting')->update(['kitchen_status' => 'processing']);

            // Re-evaluate order overall status
            $this->recalculateOrderStatus($order);

            $this->js("window.showIslandToast('Pesanan tambahan #{$order->invoice_code} mulai dimasak!', 'success');");
        } else {
            $this->js("window.showIslandToast('Pesanan sudah dibatalkan atau tidak ditemukan.', 'danger');");
        }
    }

    public function markAsReady($orderId): void
    {
        $order = Order::find($orderId);
        if ($order && $order->status !== 'cancelled') {
            // Update only processing items
            $order->items()->where('kitchen_status', 'processing')->update(['kitchen_status' => 'ready']);

            // Re-evaluate order overall status
            $this->recalculateOrderStatus($order);

            $this->js("window.showIslandToast('Kloter pesanan #{$order->invoice_code} siap disajikan!', 'success');");
        } else {
            $this->js("window.showIslandToast('Pesanan sudah dibatalkan atau tidak ditemukan.', 'danger');");
        }
    }

    public function markItemAsProcessing($itemId): void
    {
        $item = \App\Tenant\Models\Core\OrderItem::with('order')->find($itemId);
        if ($item && $item->kitchen_status === 'waiting' && $item->order && $item->order->status !== 'cancelled') {
            $item->update(['kitchen_status' => 'processing']);
            $this->recalculateOrderStatus($item->order);
            $this->js("window.showIslandToast('Item {$item->product_name} mulai dimasak!', 'success');");
        } elseif ($item && $item->order && $item->order->status === 'cancelled') {
            $this->js("window.showIslandToast('Pesanan sudah dibatalkan.', 'danger');");
        }
    }

    public function markItemAsReady($itemId): void
    {
        $item = \App\Tenant\Models\Core\OrderItem::with('order')->find($itemId);
        if ($item && $item->kitchen_status === 'processing' && $item->order && $item->order->status !== 'cancelled') {
            $item->update(['kitchen_status' => 'ready']);
            $this->recalculateOrderStatus($item->order);
            $this->js("window.showIslandToast('Item {$item->product_name} siap disajikan!', 'success');");
        } elseif ($item && $item->order && $item->order->status === 'cancelled') {
            $this->js("window.showIslandToast('Pesanan sudah dibatalkan.', 'danger');");
        }
    }

    private function recalculateOrderStatus($order)
    {
        $hasWaiting = $order->items()->where('kitchen_status', 'waiting')->exists();
        $hasProcessing = $order->items()->where('kitchen_status', 'processing')->exists();

        if ($hasWaiting) {
            $order->update(['kitchen_status' => 'waiting']);
            if (in_array($order->status, ['paid', 'pending'])) {
                $order->update(['status' => 'progress']);
            }
        } elseif ($hasProcessing) {
            $order->update(['kitchen_status' => 'processing']);
            if (in_array($order->status, ['paid', 'pending'])) {
                $order->update(['status' => 'progress']);
            }
        } else {
            $order->update(['kitchen_status' => 'ready']);
            // Check if fully paid (Direct Pay vs Open Bill)
            if ($order->amount_paid >= $order->total_price) {
                // Direct Pay -> completed
                $order->update(['status' => 'completed']);
            } else {
                // Open bill -> remains in progress until cashier pays it
                if ($order->status !== 'progress') {
                    $order->update(['status' => 'progress']);
                }
            }
        }
    }

    #[On('echo:kitchen,.KitchenUpdated')]
    public function refreshKitchen()
    {
        unset($this->kitchenBatches);
    }

    public function logout(): void
    {
        \Illuminate\Support\Facades\Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('login');
    }

    #[Computed]
    public function kitchenBatches(): array
    {
        // Performance Optimization: Select hanya kolom yang dibutuhkan untuk KDS
        $orders = Order::with(['items' => function($q) {
                $q->select('id', 'order_id', 'product_name', 'variant_name', 'note', 'quantity', 'kitchen_status', 'created_at');
            }])
            ->select('id', 'invoice_code', 'status', 'kitchen_status', 'order_type', 'table_number', 'notes', 'amount_paid', 'total_price', 'created_at', 'is_online')
            ->where(function ($query) {
                // Tampilkan pesanan yang sudah dibayar/progress
                $query->whereIn('status', ['paid', 'progress'])
                    // ATAU pesanan pending (belum bayar) HANYA JIKA dibuat secara internal (kasir/Dine-In)
                    ->orWhere(function ($q) {
                        $q->where('status', 'pending')
                            ->where('is_online', false);
                    });
            })
            ->whereIn('kitchen_status', ['waiting', 'processing'])
            ->whereDate('created_at', today())
            ->get();

        $batches = [];

        foreach ($orders as $order) {
            $waitingItems = $order->items->where('kitchen_status', 'waiting');
            if ($waitingItems->isNotEmpty()) {
                $batches[] = [
                    'order' => $order,
                    'status' => 'waiting',
                    'items' => $waitingItems,
                    'created_at' => $waitingItems->max('created_at')
                ];
            }

            $processingItems = $order->items->where('kitchen_status', 'processing');
            if ($processingItems->isNotEmpty()) {
                $batches[] = [
                    'order' => $order,
                    'status' => 'processing',
                    'items' => $processingItems,
                    'created_at' => $processingItems->max('created_at')
                ];
            }
        }

        // Urutkan batch berdasarkan waktu pemesanan (paling lama di awal)
        usort($batches, function ($a, $b) {
            return $a['created_at'] <=> $b['created_at'];
        });

        return $batches;
    }
};

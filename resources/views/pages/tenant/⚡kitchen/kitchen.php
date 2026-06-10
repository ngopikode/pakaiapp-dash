<?php

use App\Models\Order;
use Livewire\Component;

new class extends Component {
    public function markAsProcessing($orderId): void
    {
        $order = Order::find($orderId);
        if ($order) {
            // Update only waiting items
            $order->items()->where('kitchen_status', 'waiting')->update(['kitchen_status' => 'processing']);
            
            // Re-evaluate order overall status
            $this->recalculateOrderStatus($order);
            
            $this->js("window.showIslandToast('Pesanan tambahan #{$order->invoice_code} mulai dimasak!', 'success');");
        }
    }

    public function markAsReady($orderId): void
    {
        $order = Order::find($orderId);
        if ($order) {
            // Update only processing items
            $order->items()->where('kitchen_status', 'processing')->update(['kitchen_status' => 'ready']);
            
            // Re-evaluate order overall status
            $this->recalculateOrderStatus($order);
            
            $this->js("window.showIslandToast('Kloter pesanan #{$order->invoice_code} siap disajikan!', 'success');");
        }
    }
    public function markItemAsProcessing($itemId): void
    {
        $item = \App\Models\OrderItem::with('order')->find($itemId);
        if ($item && $item->kitchen_status === 'waiting') {
            $item->update(['kitchen_status' => 'processing']);
            if ($item->order) {
                $this->recalculateOrderStatus($item->order);
                $this->js("window.showIslandToast('Item {$item->product_name} mulai dimasak!', 'success');");
            }
        }
    }

    public function markItemAsReady($itemId): void
    {
        $item = \App\Models\OrderItem::with('order')->find($itemId);
        if ($item && $item->kitchen_status === 'processing') {
            $item->update(['kitchen_status' => 'ready']);
            if ($item->order) {
                $this->recalculateOrderStatus($item->order);
                $this->js("window.showIslandToast('Item {$item->product_name} siap disajikan!', 'success');");
            }
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

    public function logout(): void
    {
        \Illuminate\Support\Facades\Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        $this->redirectRoute('login');
    }

    public function with(): array
    {
        $orders = Order::with('items')
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
        usort($batches, function($a, $b) {
            return $a['created_at'] <=> $b['created_at'];
        });

        return [
            'kitchenBatches' => $batches,
        ];
    }
};

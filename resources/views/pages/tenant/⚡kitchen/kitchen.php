<?php

use App\Models\Order;
use Livewire\Component;

new class extends Component {
    public function markAsProcessing($orderId): void
    {
        $order = Order::find($orderId);
        if ($order && $order->kitchen_status === 'waiting') {
            $updateData = ['kitchen_status' => 'processing'];
            if ($order->status === 'paid') {
                $updateData['status'] = 'progress';
            }
            $order->update($updateData);
            $this->js("window.showIslandToast('Pesanan #{$order->invoice_code} mulai dimasak!', 'success');");
        }
    }

    public function markAsReady($orderId): void
    {
        $order = Order::find($orderId);
        if ($order && in_array($order->kitchen_status, ['waiting', 'processing'])) {
            $updateData = ['kitchen_status' => 'ready'];
            if ($order->status === 'paid') {
                $updateData['status'] = 'completed';
            }
            $order->update($updateData);
            $this->js("window.showIslandToast('Pesanan #{$order->invoice_code} siap disajikan!', 'success');");
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
            ->orderBy('created_at', 'asc')
            ->get();

        return [
            'kitchenOrders' => $orders,
        ];
    }
};
